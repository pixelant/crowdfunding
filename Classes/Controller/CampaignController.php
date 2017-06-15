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
            'jsVariables' => json_encode($this->jsVariables)
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
        $this->view->assignMultiple([
            'campaign' => $campaign,
            'listPid' => $this->getListPid(),
            'jsVariables' => json_encode($this->jsVariables)
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
        $responseData = array();
        $arguments = $this->request->getArguments();
        $method = isset($_POST['method']) ? $_POST['method'] : 'default';
        $hash = isset($_GET['hash']) ? $_GET['hash'] : '';
        $ajaxFunction = 'ajaxAction' . ucfirst($method);
        if (method_exists(__CLASS__, $ajaxFunction)) {
            $responseData = $this->{$ajaxFunction}();
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
        $status = null;
        $e = null;

        // $responseData['success'] = 0;
        // $responseData['message'] = 'ajajajaja nu blev det fel Mosa';
        // return $responseData;
        // Try to change amount... and it worked anyway...
        // $amount = $amount * 1.25;

        try {

            $campaign = $this->campaignRepository->findByUid($campaignId);
            foreach ($campaign->getPledges() as $key => $item) {
                if ($item->getUid() == $pledgeId) {
                    $pledge = $item;
                }
            }
            $backer = $this->getBacker($email);
            $transaction = GeneralUtility::makeInstance(
                \Pixelant\Crowdfunding\Domain\Model\Transaction::class
            );
            $transaction->setReference(json_encode($_POST['stripeToken']));
            $transaction->setCampaignId($campaign->getUid());
            $transaction->setPledgingId($pledge->getUid());
            $transaction->setPid($campaign->getPid());
            \Stripe\Stripe::setApiKey($this->settings['stripe']['secretKey']);

            $customer = \Stripe\Customer::create([
                'email' => $email,
                'source'  => $token['id']
            ]);

            // TODO: if pledgeid, check if $pledge->getAmount() is less than amount then throw error....

            $charge = \Stripe\Charge::create([
                'customer' => $customer->id,
                'amount'   => $amount * 100,
                'currency' => $this->settings['stripe']['currency']
            ]);

            $transaction->setStatus($status);
            $transaction->setAmount($pledge->getAmount());
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
        } catch(\Exception $e) {
            $responseData['success'] = 0;
            $responseData['message'] = $e->getMessage();
        } catch(\Stripe\Error\InvalidRequest $e) {
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
        $toString = (int)$_POST['toString'];
        $message = [];

        try {
            $campaign = $this->campaignRepository->findByUid($campaignId);
            if ($campaign instanceof \Pixelant\Crowdfunding\Domain\Model\Campaign) {
                if ($toString) {
                    $message['pledged'] = $campaign->getPledgedAsString();
                    $message['totalBackedAmount'] = $campaign->getTotalBackedAmountAsString();
                } else {
                    $message['pledged'] = $campaign->getPledged();
                    $message['totalBackedAmount'] = $campaign->getTotalBackedAmount();
                }
                $message['backers'] = count($campaign->getBackers());
                $message['totalBackedAmountPercent'] = $campaign->getTotalBackedAmountPercent() . '%';

                foreach ($campaign->getPledges() as $pledge) {
                    $keyPrefix = 'pledge_' . $pledge->getUid() . '_';
                    if ($toString) {
                        $message[$keyPrefix . 'totalBackedAmount'] = $pledge->getTotalBackedAmountAsString();
                    } else {
                        $message[$keyPrefix . 'totalBackedAmount'] = $pledge->getTotalBackedAmount();
                    }
                }
            }
        } catch (\Exception $e) {
            $message = $e->getMessage();
        }

        $responseData['success'] = $campaign instanceof \Pixelant\Crowdfunding\Domain\Model\Campaign;
        $responseData['message'] = $message;

        return $responseData;
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
}
