<?php
namespace Pixelant\Crowdfunding\Tests\Unit\Controller;

/**
 * Test case.
 */
class CampaignControllerTest extends \TYPO3\CMS\Core\Tests\UnitTestCase
{
    /**
     * @var \Pixelant\Crowdfunding\Controller\CampaignController
     */
    protected $subject = null;

    protected function setUp()
    {
        parent::setUp();
        $this->subject = $this->getMockBuilder(\Pixelant\Crowdfunding\Controller\CampaignController::class)
            ->setMethods(['redirect', 'forward', 'addFlashMessage'])
            ->disableOriginalConstructor()
            ->getMock();
    }

    protected function tearDown()
    {
        parent::tearDown();
    }

    /**
     * @test
     */
    public function listActionFetchesAllCampaignsFromRepositoryAndAssignsThemToView()
    {
        $allCampaigns = $this->getMockBuilder(\TYPO3\CMS\Extbase\Persistence\ObjectStorage::class)
            ->disableOriginalConstructor()
            ->getMock();

        $campaignRepository = $this->getMockBuilder(\Pixelant\Crowdfunding\Domain\Repository\CampaignRepository::class)
            ->setMethods(['findAll'])
            ->disableOriginalConstructor()
            ->getMock();
        $campaignRepository->expects(self::once())->method('findAll')->will(self::returnValue($allCampaigns));
        $this->inject($this->subject, 'campaignRepository', $campaignRepository);

        $view = $this->getMockBuilder(\TYPO3\CMS\Extbase\Mvc\View\ViewInterface::class)->getMock();
        $view->expects(self::once())->method('assign')->with('campaigns', $allCampaigns);
        $this->inject($this->subject, 'view', $view);

        $this->subject->listAction();
    }

    /**
     * @test
     */
    public function showActionAssignsTheGivenCampaignToView()
    {
        $campaign = new \Pixelant\Crowdfunding\Domain\Model\Campaign();

        $view = $this->getMockBuilder(\TYPO3\CMS\Extbase\Mvc\View\ViewInterface::class)->getMock();
        $this->inject($this->subject, 'view', $view);
        $view->expects(self::once())->method('assign')->with('campaign', $campaign);

        $this->subject->showAction($campaign);
    }
}
