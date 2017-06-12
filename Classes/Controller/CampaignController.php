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

    public function initializeAction()
    {
        if (empty($this->settings['currency']['thousandsSeparator'])) {
            $this->settings['currency']['thousandsSeparator'] = ' ';
        }
        if (empty($this->settings['currency']['decimalSeparator'])) {
            $this->settings['currency']['decimalSeparator'] = ' ';
        }
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
        $this->view->assign('campaigns', $campaigns);
        $this->view->assign('detailPid', $this->getDetailPid());
    }

    /**
     * action show
     *
     * @param Pixelant\Crowdfunding\Domain\Model\Campaign
     * @return void
     */
    public function showAction(\Pixelant\Crowdfunding\Domain\Model\Campaign $campaign)
    {
        $this->view->assign('campaign', $campaign);
        $this->view->assign('listPid', $this->getListPid());
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
