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
     * action list
     *
     * @param Pixelant\Crowdfunding\Domain\Model\Campaign
     * @return void
     */
    public function listAction()
    {
        $projects = $this->campaignRepository->findAll();
        $this->view->assign('campaigns', $campaigns);
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
    }
}
