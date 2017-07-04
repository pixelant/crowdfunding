<?php
namespace Pixelant\Crowdfunding\Tests\Unit\Controller;

use Pixelant\Crowdfunding\Controller\CampaignController;
use TYPO3\CMS\Extbase\Mvc\Web\Request;
use TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Service\ExtensionService;
use TYPO3\CMS\Extbase\Service\EnvironmentService;

/**
 * Test case.
 */
class CampaignControllerTest extends \Nimut\TestingFramework\TestCase\UnitTestCase
{
    /**
     * @var \Pixelant\Crowdfunding\Controller\CampaignController
     */
    protected $subject = null;
    
    /**
     * @var string
     */
    protected $buildUriAjax = 'https:/localhost';

    protected function setUp()
    {
        parent::setUp();
        $this->subject = $this->getAccessibleMock(CampaignController::class, ['dummy', 'buildUriAjax']);
    }

    protected function tearDown()
    {
        parent::tearDown();
    }

    /**
     * @test
     */
    public function initializeActionSetsExpectedJsVariables()
    {
        $this->subject
            ->expects(self::once())
            ->method('buildUriAjax')
            ->will($this->returnValue($this->buildUriAjax));

        $this->inject(
            $this->subject,
            'settings',
            [
                'ajaxPageType' => '201706291',
                'stripe' => [
                    'publishableKey' => 'pubKey',
                    'currency' => '€',
                    'name' => 'Name Of Site',
                    'image' => ''
                ],
                'listPid' => 2,
                'detailPid' => 3
            ]
        );
        $this->subject->initializeAction();

        $jsVariables = $this->subject->_get('jsVariables');

        $this->assertEquals($jsVariables['uriAjax'], $this->buildUriAjax);

        $this->assertEquals($jsVariables['stripe']['name'], 'Name Of Site');

        $this->assertEquals($jsVariables['stripe']['publishableKey'], 'pubKey');

        $this->assertEquals($jsVariables['stripe']['currency'], '€');

        $this->assertEquals($jsVariables['stripe']['image'], '');

        $this->assertEmpty(
            $jsVariables['stripe']['secretKey'],
            'The setting stripe.secretKey should never be added to jsVariables'
        );
    }

    /**
     * @test
     */
    public function listActionFetchesAllCampaignsFromRepositoryAndAssignsExpectedVariablesToView()
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

        $detailPid = $this->subject->_call('getDetailPid');
        $jsLabels = json_encode($this->subject->_call('getLocalizedFrontendLabels'));
        $jsVariables = json_encode($this->subject->_get('jsVariables'));

        $expected = [
            'campaigns' => $allCampaigns,
            'detailPid' => $detailPid,
            'jsVariables' => $jsVariables,
            'jsLabels' => $jsLabels
        ];

        $view->expects(self::once())->method('assignMultiple')->with($expected);
        $this->inject($this->subject, 'view', $view);

        $this->subject->listAction();
    }

    /**
     * @test
     */
    public function showActionAssignsExcpectedVariablesToView()
    {
        $campaign = new \Pixelant\Crowdfunding\Domain\Model\Campaign();

        $listPid = $this->subject->_call('getListPid');
        $disableStripe = $_SERVER['HTTPS'] === null ? 1 : 0;
        $jsLabels = json_encode($this->subject->_call('getLocalizedFrontendLabels'));
        $jsVariables = json_encode($this->subject->_get('jsVariables'));

        $expected = [
            'campaign' => $campaign,
            'listPid' => $listPid,
            'disableStripe' => $disableStripe,
            'jsVariables' => $jsVariables,
            'jsLabels' => $jsLabels
        ];
        
        $view = $this->getMockBuilder(\TYPO3\CMS\Extbase\Mvc\View\ViewInterface::class)->getMock();
        $this->inject($this->subject, 'view', $view);

        $view->expects(self::once())->method('assignMultiple')->with($expected);

        $this->subject->showAction($campaign);
    }

    /**
     * @test
     */
    public function getBackerByEmailFetchesAndReturnsExistingBacker()
    {
        $email = 'john.doe@test.com';
        $backer = new \Pixelant\Crowdfunding\Domain\Model\Backer();
        $backer->setEmail($email);

        $backerRepository = $this->getMockBuilder(\Pixelant\Crowdfunding\Domain\Repository\BackerRepository::class)
            ->setMethods(['findOneByEmail','add'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->inject($this->subject, 'backerRepository', $backerRepository);

        $backerRepository
            ->expects(self::once())
            ->method('findOneByEmail')
            ->with($email)
            ->will($this->returnValue($backer));

        $backerRepository
            ->expects(self::never())
            ->method('add');
            
        $result = $this->subject->_call('getBacker', $email);

        $this->assertEquals($backer, $result);
    }

    /**
     * @test
     */
    public function getBackerByEmailAndNoBackerExistsReturnsNewBacker()
    {
        $email = 'john.doe@test.com';
        $backer = new \Pixelant\Crowdfunding\Domain\Model\Backer();
        $backer->setEmail($email);

        $backerRepository = $this->getMockBuilder(\Pixelant\Crowdfunding\Domain\Repository\BackerRepository::class)
            ->setMethods(['findOneByEmail','add'])
            ->disableOriginalConstructor()
            ->getMock();
        
        $this->inject($this->subject, 'backerRepository', $backerRepository);

        $backerRepository
            ->expects(self::once())
            ->method('findOneByEmail')
            ->with($email)
            ->will($this->returnValue(null));

        $backerRepository
            ->expects(self::once())
            ->method('add');

        $result = $this->subject->_call('getBacker', $email);

        $this->assertEquals($backer, $result);
    }

    /**
     * @test
     * @expectedException \Exception
     */
    public function getBackerByEmailWithoutEmailThrowsException()
    {
        $email = '';
        $result = $this->subject->_call('getBacker', $email);
    }
}
