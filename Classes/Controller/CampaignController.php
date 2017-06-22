<?php
namespace Pixelant\Crowdfunding\Controller;

/***
 *
 * This file is part of the "Crowdfunding" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 *  (c) 2017
 *
 ***/

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;
use Pixelant\Crowdfunding\Utility\CrowdfundingUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

/**
 * CampaignController
 */
class CampaignController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{
    /**
     * campaignRepository
     *
     * @var \Pixelant\Crowdfunding\Domain\Repository\CampaignRepository
     * @inject
     */
    protected $campaignRepository = null;

    /**
     * backerRepository
     *
     * @var \Pixelant\Crowdfunding\Domain\Repository\BackerRepository
     * @inject
     */

    protected $backerRepository = null;

    /**
     * transactionRepository
     *
     * @var \Pixelant\Crowdfunding\Domain\Repository\TransactionRepository
     * @inject
     */
    protected $transactionRepository = null;

    /**
     * javascript variables
     *
     * @var array
     */
    protected $jsVariables = [];

    public function initializeAction()
    {
        // Manually set variables for javascript to avoid f.ex. secret settings to get rendered into page
        $this->jsVariables['uriAjax'] = $this->buildUriAjax();
        $this->jsVariables['stripe']['publishableKey'] = $this->settings['stripe']['publishableKey'];
        $this->jsVariables['stripe']['currency'] = $this->settings['stripe']['currency'];
        $this->jsVariables['stripe']['name'] = $this->settings['stripe']['name'];
        $this->jsVariables['stripe']['image'] = $this->settings['stripe']['image'];
    }

    /**
     * action list
     *
     * @param Pixelant\Crowdfunding\Domain\Model\Campaign
     * @return void
     */
    public function listAction()
    {
        $campaigns = $this->campaignRepository->findAll();
        $this->view->assignMultiple([
            'campaigns' => $campaigns,
            'detailPid' => $this->getDetailPid(),
            'jsVariables' => json_encode($this->jsVariables),
            'jsLabels' => json_encode($this->getLocalizedFrontendLabels())
        ]);
    }

    /**
     * action show
     *
     * @param Pixelant\Crowdfunding\Domain\Model\Campaign
     * @return void
     */
    public function showAction(\Pixelant\Crowdfunding\Domain\Model\Campaign $campaign)
    {
        $this->jsVariables['token'] = $formProtectToken;
        $this->view->assignMultiple([
            'campaign' => $campaign,
            'listPid' => $this->getListPid(),
            'token' => $this->getToken($formProtectToken),
            'jsVariables' => json_encode($this->jsVariables),
            'jsLabels' => json_encode($this->getLocalizedFrontendLabels())
        ]);
    }

    /**
     * action ajax
     *
     * @param Pixelant\Crowdfunding\Domain\Model\Campaign
     * @return void
     */
    public function ajaxAction()
    {

        // TODO: maybe simple check of $_SERVER['HTTP_X_REQUESTED_WITH'], $_SERVER['HTTP_REFERER'] or $_SERVER['HTTP_ORIGIN']

        $responseData = array();
        $arguments = $this->request->getArguments();
        $method = isset($_POST['method']) ? $_POST['method'] : 'default';
        $hash = isset($_GET['hash']) ? $_GET['hash'] : '';
        $ajaxFunction = 'ajaxAction' . ucfirst($method);
        if (method_exists(__CLASS__, $ajaxFunction)) {
            try {
                $responseData = $this->{$ajaxFunction}();
            } catch (\Exception $e) {
                $responseData['success'] = 0;
                $responseData['message'] = $e->getMessage();
            }
        } else {
            $responseData['success'] = 0;
            $responseData['message'] = 'Unknown method (' . $ajaxFunction . ')';
        }
        return json_encode($responseData);
    }

    /**
     * action ajax
     *
     * @param Pixelant\Crowdfunding\Domain\Model\Campaign
     * @return array
     */
    public function ajaxActionCharge()
    {
        $responseData = array();
        $campaign = null;
        $pledge = null;
        $campaignId = (int)$_POST['campaignId'];
        $pledgeId = (int)$_POST['pledgeId'];
        $token  = $_POST['stripeToken'];
        $amount  = $_POST['amount'];
        $email = $token['email'];
        $checksum = $_POST['checksum'];
        $status = null;
        $e = null;

        try {
            // get campaign
            $campaign = $this->getCampaign($campaignId);
            if (!$campaign) {
                throw new \Exception("Error Processing Request", 1);
            }

            // compare checksums
            $buildChecksum = $this->getChecksum(
                $campaignId,
                $pledgeId,
                $amount
            );
            if ($buildChecksum != $checksum) {
                throw new \Exception("Checksum mismatch", 1);
            }

            // fetch pledge if selected
            $pledge = $this->getCampaignPledge($pledgeId);
            
            // find backer
            $backer = $this->getBacker($email);

            // create new transaction
            $transaction = GeneralUtility::makeInstance(
                \Pixelant\Crowdfunding\Domain\Model\Transaction::class
            );
            // Set transaction properties
            $transaction->setReference(json_encode($_POST['stripeToken']));
            $transaction->setCampaignId($campaign->getUid());
            if ($pledge) {
                $transaction->setPledgingId($pledge->getUid());
            }
            $transaction->setPid($campaign->getPid());

            // Stripe - set
            \Stripe\Stripe::setApiKey($this->settings['stripe']['secretKey']);

            $customer = \Stripe\Customer::create([
                'email' => $email,
                'source'  => $token['id']
            ]);

            // TODO: if pledgeid, check if $pledge->getAmount() is less than amount then throw error....

            $charge = \Stripe\Charge::create([
                'customer' => $customer->id,
                'amount'   => $amount * 100,
                'currency' => $this->settings['stripe']['currency'],
                'description' => $campaign->getTitle(),
                'metadata' => [
                    'campaign_id' => $campaign->getUid(),
                    'pledge_id' => !empty($plegde) ? $pledge->getUid() : 0,
                    'backer_id' => $backer->getUid()
                ]
            ]);

            $transaction->setStatus($status);
            $transaction->setAmount($amount);
            $this->transactionRepository->add($transaction);
            $backer->addTransaction($transaction);
            $backer->setPid($campaign->getPid());
            // $this->backerRepository->update($backer);
            $campaign->addBacker($backer);
            $this->campaignRepository->update($campaign);
            $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
            $objectManager->get(PersistenceManager::class)->persistAll();

            $cacheManager = GeneralUtility::makeInstance(\TYPO3\CMS\Core\Cache\CacheManager::class);
            $cacheManager->flushCachesInGroupByTag('pages', 'crowdfunding');
            $responseData['success'] = 1;
            $responseData['message'] = 'Thank you message ... ' . $amount;
        } catch (\Exception $e) {
            $responseData['success'] = 0;
            $responseData['message'] = $e->getMessage();
        } catch (\Stripe\Error\InvalidRequest $e) {
            $responseData['success'] = 0;
            $responseData['message'] = $e->getMessage();
        }
        /*
        // @TODO: Start of debug, remember to remove when debug is done!
        \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump(
            array(
                'details' => array('@' => date('Y-m-d H:i:s'), 'class' => __CLASS__, 'function' => __FUNCTION__, 'file' => __FILE__, 'line' => __LINE__),
                'campaign' => $campaign,
                'pledge' => $pledge,
                'token' => $token,
                'email' => $email,
                'backer' => $backer,
                'transaction' => $transaction,
                'customer' => $customer,
                'charge' => $charge,
                'error' => $e
            )
            ,date('Y-m-d H:i:s') . ' : ' . __METHOD__ . ' : ' . __LINE__
        );
        // @TODO: End of debug, remember to remove when debug is done!
        */
        return $responseData;
    }

    /**
     * action ajax campaignNumbers
     *
     * @param Pixelant\Crowdfunding\Domain\Model\Campaign
     * @return array
     */
    public function ajaxActionCampaignNumbers()
    {
        $campaignId = (int)$_POST['campaignId'];
        $campaign = $this->getCampaign($campaignId);
        $toString = (int)$_POST['toString'];
        $message = [];
        $success = false;

        // throw exception if campaign wasn't found, shouldn't happen
        if (!$campaign) {
            $this->logError(__FUNCTION__ . ', campaign was not fetched');
            throw new \Exception(LocalizationUtility::translate('label.campaign.notfound', 'Crowdfunding'));
        }

        try {
            // fetch properties which also can be fetched as string
            if ($toString) {
                $message['pledged'] = $campaign->getPledgedAsString();
                $message['totalBackedAmount'] = $campaign->getTotalBackedAmountAsString();
            } else {
                $message['pledged'] = $campaign->getPledged();
                $message['totalBackedAmount'] = $campaign->getTotalBackedAmount();
            }

            // fetch properties without option to be fetched as string
            $message['backers'] = count($campaign->getBackers());
            $message['totalBackedAmountPercent'] = $campaign->getTotalBackedAmountPercent() . '%';

            // fetch pledge properties
            foreach ($campaign->getPledges() as $pledge) {
                // set prefix so js can find objects
                $keyPrefix = 'pledge_' . $pledge->getUid() . '_';
                // different properties if toString
                if ($toString) {
                    $message[$keyPrefix . 'totalBackedAmount'] = $pledge->getTotalBackedAmountAsString();
                } else {
                    $message[$keyPrefix . 'totalBackedAmount'] = $pledge->getTotalBackedAmount();
                }
            }
            $success = true;
        } catch (\Exception $e) {
            $this->logError(__FUNCTION__ . ', exception caught: ' . $e->getMessage());
            $message = $e->getMessage();
        }

        $responseData['success'] = $success;
        $responseData['message'] = $message;

        return $responseData;
    }

    /**
     * action checksum
     *
     * @return array
     */
    public function ajaxActionChecksum()
    {
        try {
            $checksum = $this->getChecksum(
                (int)$_POST['campaignId'],
                (int)$_POST['pledgeId'],
                $_POST['amount']
            );
            $responseData['success'] = 1;
            $responseData['message'] = $checksum;
        } catch (\Exception $e) {
            $responseData['success'] = 1;
            $responseData['message'] = $e->getMessage();
        }
        return $responseData;
    }

    /**
     * action checksum
     *
     * @return array
     */
    public function ajaxActionIsAmountValid()
    {
        $campaignId = (int)$_POST['campaignId'];
        $campaign = $this->getCampaign($campaignId);
        $amount = (float)$_POST['amount'];
        $isAmountValid = false;
        $message = '';
        if ($campaign) {
            if ($amount >= $campaign->getMinAmount()) {
                $isAmountValid = true;
                $message = CrowdfundingUtility::formatCurrency($amount);
            } else {
                $message = 'Minimum amount to back is ' . $campaign->getMinAmount() . '  (' . $amount . ')';
            }
        } else {
            $message = 'Couldn\'t verify if amount is valid, campaign was not found';
        }
        return  [
            'success' => $isAmountValid ? 1 : 0,
            'message' => $message
        ];
    }

    /**
     * Build base ajax uri
     *
     * @return string
     */
    protected function buildUriAjax()
    {
        return $this->uriBuilder
            ->reset()
            ->setTargetPageUid($GLOBALS['TSFE']->id)
            ->setUseCacheHash(false)
            ->setCreateAbsoluteUri(true)
            ->setTargetPageType($this->settings['ajaxPageType'])
            ->build();
    }

    /**
     * get page id to "detail" page
     * 
     * @return int
     */
    protected function getDetailPid()
    {
        $currentId = $GLOBALS['TSFE']->id;
        $detailPid = (int)$this->settings['detailPid'];
        return $detailPid > 0 ? $detailPid : $currentId;
    }

    /**
     * get page id to "list" page
     * 
     * @return int
     */
    protected function getListPid()
    {
        $currentId = $GLOBALS['TSFE']->id;
        $listPid = (int)$this->settings['listPid'];
        return $listPid > 0 ? $listPid : $currentId;
    }

    /**
     * get "backer" by email, returns first with same email or creates a new
     * 
     * @param string $email
     *
     * @return \Pixelant\Crowdfunding\Domain\Model\Backer
     */
    protected function getBacker($email)
    {
        // TODO: check storagepid, seems not to find any record when call is from js (ajax) 
        $backer = $this->backerRepository->findOneByEmail($email);
        if (!$backer instanceof \Pixelant\Crowdfunding\Domain\Model\Backer) {
            $backer = GeneralUtility::makeInstance(
                \Pixelant\Crowdfunding\Domain\Model\Backer::class
            );
            $backer->setEmail($email);
            $this->backerRepository->add($backer);
        }
        return $backer;
    }

    /**
     * get "campaign" by id, returns campaign or false
     *
     * @param int $campaignId
     *
     * @return \Pixelant\Crowdfunding\Domain\Model\Campaign|false
     */
    protected function getCampaign($campaignId)
    {
        $campaign = false;
        // Find campaign
        if ($campaignId > 0) {
            $findCampaign = $this->campaignRepository->findByUid($campaignId);
            if ($findCampaign instanceof \Pixelant\Crowdfunding\Domain\Model\Campaign) {
                $campaign = $findCampaign;
            }
        }
        return $campaign;
    }

    /**
     * get campaign pledge by id, returns pledge or false
     *
     * @param \Pixelant\Crowdfunding\Domain\Model\Campaign $campaign
     * @param int $pledgeId
     *
     * @return \Pixelant\Crowdfunding\Domain\Model\Pledge|false
     */
    protected function getCampaignPledge($campaign, $pledgeId)
    {
        $pledge = false;
        if ($campaign instanceof \Pixelant\Crowdfunding\Domain\Model\Campaign &&
            $pledgeId > 0) {
            $campaignPledges = $campaign->getPledges();
            foreach ($campaignPledges as $key => $campaignPledge) {
                if ($campaignPledge->getUid() === $pledgeId) {
                    $pledge = $campaignPledge;
                    break;
                }
            }
        }
        return $pledge;
    }

    /**
     * generate checksum
     *
     * @param int $campaignId
     * @param int $pledgeId
     * @param float $amount
     *
     * @return string
     */
    protected function getChecksum($campaignId, $pledgeId, $amount)
    {
        $campaign = $this->getCampaign($campaignId);
        $pledge = $this->getCampaignPledge($campaign, $pledgeId);
        $checksum = '';

        // check if a campaign is found
        if (!$campaign) {
            throw new \Exception("Cannot generate checksum without a valid campaign");
        }

        // check that amount is equal to pledge amount if pledge is set
        if ($pledge && $amount != $pledge->getAmount()) {
            throw new \Exception("The specified amount is not equal to amount in selected pledge", 1);
        }

        // check that amount is greater than campaing min pledgeAmount
        if ($amount < $campaign->getMinAmount()) {
            throw new \Exception("The specified amount is smaller than minimum amount set in campaign", 1);
        }

        $crdate = $campaign->getCrdate();
        $checksum = $campaignId . '|' . $crdate . '|' . $pledgeId . '|' . $amount;
        return hash('sha256', $checksum);
    }

    /**
     * Localize labels
     *
     * @return array
     */
    protected function getLocalizedFrontendLabels()
    {
        $languageFactory = GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Localization\\LocalizationFactory');
        $parsedLocallang = $languageFactory->getParsedData(
            'EXT:crowdfunding/Resources/Private/Language/locallang.xlf',
            'default'
        );
        $localizedLabels = [];
        foreach (array_keys($parsedLocallang['default']) as $key) {
            $localizedLabels[$key] = LocalizationUtility::translate($key, 'Crowdfunding');
        }
        return $localizedLabels;
    }

    protected function logError($message)
    {
        /** @var $logger \TYPO3\CMS\Core\Log\Logger */
        $logger = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Core\Log\LogManager')->getLogger(__CLASS__);
        $data['requestUri'] = $this->request->getRequestUri();
        $data['method'] = $this->request->getMethod();
        $data['get'] = $_GET;
        $data['post'] = $_POST;
        $data['HTTP_REFERER'] = $_SERVER['HTTP_REFERER'];
        $data['HTTP_USER_AGENT'] = $_SERVER['HTTP_USER_AGENT'];
        $data['REMOTE_ADDR'] = $_SERVER['REMOTE_ADDR'];
        $logger->error($message, $data);
    }

    protected function getToken($campaignId)
    {
        $token = \TYPO3\CMS\Core\FormProtection\FormProtectionFactory::get(
            \Pixelant\Crowdfunding\FormProtection\CrowdfundingFormProtection::class
        )
        ->generateToken('tx_crowdfunding_domain_model_campaign', 'show', $campaignId);
        return $token;
    }
}
