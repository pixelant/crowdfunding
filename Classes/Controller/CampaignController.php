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
     * javascript variables
     *
     * @var array
     */
    protected $jsVariables = [];

    public function initializeAction()
    {
        // Replace empty currency settings with spaces
        if (empty($this->settings['currency']['thousandsSeparator'])) {
            $this->settings['currency']['thousandsSeparator'] = ' ';
        }
        if (empty($this->settings['currency']['decimalSeparator'])) {
            $this->settings['currency']['decimalSeparator'] = ' ';
        }
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
     * @return void
     */
    public function ajaxActionCharge()
    {
        $token = $_POST['token'];
        $pledgeId = $_POST['pledgeId'];
        $responseData = array();
        $arguments = $this->request->getArguments();
        $responseData['success'] = 1;
        $responseData['message'] = $arguments;
        $responseData['token'] = $token;
        $responseData['pledgeId'] = $pledgeId;
        return json_encode($responseData);
    }

    /**
     * action checkout
     *
     * @param Pixelant\Crowdfunding\Domain\Model\Campaign
     * @return void
     */
    public function checkoutAction()
    {
        $campaigns = $this->campaignRepository->findAll();
        $this->view->assign('campaigns', $campaigns);
        $this->view->assign('detailPid', $this->getDetailPid());
    }

    /**
     * action charge
     *
     * @param Pixelant\Crowdfunding\Domain\Model\Campaign
     * @return void
     */
    public function chargeAction()
    {
        $campaigns = $this->campaignRepository->findAll();
        $this->view->assign('campaigns', $campaigns);
        $this->view->assign('detailPid', $this->getDetailPid());
        $token  = $_POST['stripeToken'];

        \Stripe\Stripe::setApiKey($this->settings['stripe']['secretKey']);

        $customer = \Stripe\Customer::create([
            'email' => 'mats@pixelant.se',
            'source'  => $token
        ]);

        $charge = \Stripe\Charge::create([
            'customer' => $customer->id,
            'amount'   => 50,
            'currency' => 'sek'
        ]);

        
        // @TODO: Start of debug, remember to remove when debug is done!
        \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump(
            array(
                'details' => array('@' => date('Y-m-d H:i:s'), 'class' => __CLASS__, 'function' => __FUNCTION__, 'file' => __FILE__, 'line' => __LINE__),
                'charge' => $charge,
            )
            ,date('Y-m-d H:i:s') . ' : ' . __METHOD__ . ' : ' . __LINE__
        );
        // @TODO: End of debug, remember to remove when debug is done!
        
        echo '<h1>Successfully charged 50!</h1>';
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
}
