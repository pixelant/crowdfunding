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

    /**
     * @test
     */
    public function getCampaignFetchesAndReturnsExistingCampaign()
    {
        $campaignId = 1;
        $campaign = new \Pixelant\Crowdfunding\Domain\Model\Campaign();

        $campaignRepository = $this->getMockBuilder(\Pixelant\Crowdfunding\Domain\Repository\CampaignRepository::class)
            ->setMethods(['findByUid'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->inject($this->subject, 'campaignRepository', $campaignRepository);

        $campaignRepository
            ->expects(self::once())
            ->method('findByUid')
            ->with($campaignId)
            ->will($this->returnValue($campaign));

        $result = $this->subject->_call('getCampaign', $campaignId);

        $this->assertEquals($campaign, $result);
        $this->assertNotEquals(false, $result);
    }

    /**
     * @test
     */
    public function getCampaignFetchesAndReturnsFalseWithNoExistingCampaign()
    {
        $campaignId = 1;
        $campaign = null;

        $campaignRepository = $this->getMockBuilder(\Pixelant\Crowdfunding\Domain\Repository\CampaignRepository::class)
            ->setMethods(['findByUid'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->inject($this->subject, 'campaignRepository', $campaignRepository);

        $campaignRepository
            ->expects(self::once())
            ->method('findByUid')
            ->with($campaignId)
            ->will($this->returnValue($campaign));

        $result = $this->subject->_call('getCampaign', $campaignId);

        $this->assertEquals(false, $result);
    }

    /**
     * @test
     */
    public function getCampaignFetchesAndReturnsFalseWithMissingCampaignId()
    {
        $campaignId = 0;
        $campaign = null;

        $campaignRepository = $this->getMockBuilder(\Pixelant\Crowdfunding\Domain\Repository\CampaignRepository::class)
            ->setMethods(['findByUid'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->inject($this->subject, 'campaignRepository', $campaignRepository);

        $campaignRepository
            ->expects(self::never())
            ->method('findByUid')
            ->with($campaignId)
            ->will($this->returnValue($campaign));

        $result = $this->subject->_call('getCampaign', $campaignId);

        $this->assertEquals(false, $result);
    }

    /**
     * @test
     */
    public function getCampaignPledgeFetchesAndReturnsExistingPledge()
    {
        $pledgeId = 1;
        $campaign = new \Pixelant\Crowdfunding\Domain\Model\Campaign();
        $mockPledge = $this->getMockBuilder(\Pixelant\Crowdfunding\Domain\Model\Pledging::class)
            ->setMethods(['getUid'])
            ->disableOriginalConstructor()
            ->getMock();

        $mockPledge
            ->expects(self::once())
            ->method('getUid')
            ->will($this->returnValue($pledgeId));

        $campaign->addPledge($mockPledge);

        $result = $this->subject->_call('getCampaignPledge', $campaign, $pledgeId);

        $this->assertEquals($mockPledge, $result);
        $this->assertNotEquals(false, $result);
    }

    /**
     * @test
     */
    public function getCampaignPledgeFetchesAndReturnsFalseForNonExistingPledge()
    {
        $pledgeId = 1;
        $campaign = new \Pixelant\Crowdfunding\Domain\Model\Campaign();
        $mockPledge = $this->getMockBuilder(\Pixelant\Crowdfunding\Domain\Model\Pledging::class)
            ->setMethods(['getUid'])
            ->disableOriginalConstructor()
            ->getMock();

        $mockPledge
            ->expects(self::once())
            ->method('getUid')
            ->will($this->returnValue(9999));

        $campaign->addPledge($mockPledge);

        $result = $this->subject->_call('getCampaignPledge', $campaign, $pledgeId);

        $this->assertEquals(false, $result);
    }

    /**
     * @test
     */
    public function getCampaignDoesNotFetchPledgeAndReturnsFalseWhenPledgeIdIsMissing()
    {
        $pledgeId = null;
        $campaign = new \Pixelant\Crowdfunding\Domain\Model\Campaign();
        $mockPledge = $this->getMockBuilder(\Pixelant\Crowdfunding\Domain\Model\Pledging::class)
            ->setMethods(['getUid'])
            ->disableOriginalConstructor()
            ->getMock();

        $mockPledge
            ->expects(self::never())
            ->method('getUid')
            ->will($this->returnValue($pledgeId));

        $campaign->addPledge($mockPledge);

        $result = $this->subject->_call('getCampaignPledge', $campaign, $pledgeId);

        $this->assertEquals(false, $result);
    }

    /**
     * @test
     */
    public function getMatchChecksumForCampaingAndPledge()
    {
        $campaignId = 15;
        $pledgeId = 20;
        $amount = 30;
        $crdate = 112223334445;
        $checksum = hash('sha256', $campaignId . '|' . $crdate . '|' . $pledgeId . '|' . $amount);

        $mockPledge = $this->getMockBuilder(\Pixelant\Crowdfunding\Domain\Model\Pledging::class)
            ->setMethods(['getUid', 'getAmount'])
            ->disableOriginalConstructor()
            ->getMock();

        $mockPledge
            ->expects(self::once())
            ->method('getUid')
            ->will($this->returnValue($pledgeId));

        $mockPledge
            ->expects(self::once())
            ->method('getAmount')
            ->will($this->returnValue($amount));

        $mockCampaign = $this->getMockBuilder(\Pixelant\Crowdfunding\Domain\Model\Campaign::class)
            ->setMethods(['getCrdate', 'getPledges'])
            ->disableOriginalConstructor()
            ->getMock();

        $mockCampaign
            ->expects(self::once())
            ->method('getPledges')
            ->will($this->returnValue(['mockPledge' => $mockPledge]));

        $mockCampaign
            ->expects(self::once())
            ->method('getCrdate')
            ->will($this->returnValue($crdate));

        $campaignRepository = $this->getMockBuilder(\Pixelant\Crowdfunding\Domain\Repository\CampaignRepository::class)
            ->setMethods(['findByUid'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->inject($this->subject, 'campaignRepository', $campaignRepository);

        $campaignRepository
            ->expects(self::once())
            ->method('findByUid')
            ->with($campaignId)
            ->will($this->returnValue($mockCampaign));

        $result = $this->subject->_call('getChecksum', $campaignId, $pledgeId, $amount);

        $this->assertEquals($checksum, $result);
    }

    /**
     * @test
     */
    public function getMatchChecksumForCampaingWithoutPledge()
    {
        $campaignId = 15;
        $pledgeId = 0;
        $amount = 30;
        $crdate = 112223334445;
        $checksum = hash('sha256', $campaignId . '|' . $crdate . '|' . $pledgeId . '|' . $amount);

        $mockCampaign = $this->getMockBuilder(\Pixelant\Crowdfunding\Domain\Model\Campaign::class)
            ->setMethods(['getCrdate'])
            ->disableOriginalConstructor()
            ->getMock();

        $mockCampaign
            ->expects(self::once())
            ->method('getCrdate')
            ->will($this->returnValue($crdate));

        $campaignRepository = $this->getMockBuilder(\Pixelant\Crowdfunding\Domain\Repository\CampaignRepository::class)
            ->setMethods(['findByUid'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->inject($this->subject, 'campaignRepository', $campaignRepository);

        $campaignRepository
            ->expects(self::once())
            ->method('findByUid')
            ->with($campaignId)
            ->will($this->returnValue($mockCampaign));

        $result = $this->subject->_call('getChecksum', $campaignId, $pledgeId, $amount);

        $this->assertEquals($checksum, $result);
    }

    /**
     * @test
     * @expectedException \Exception
     */
    public function getChecksumForCampaingWithToLowAmountThrowsException()
    {
        $campaignId = 15;
        $pledgeId = 0;
        $amount = 5;
        $minAmount = 10;
        $crdate = 112223334445;
        $checksum = hash('sha256', $campaignId . '|' . $crdate . '|' . $pledgeId . '|' . $amount);

        $mockCampaign = $this->getMockBuilder(\Pixelant\Crowdfunding\Domain\Model\Campaign::class)
            ->setMethods(['getUid', 'getMinAmount', 'getCrdate', 'getPledges'])
            ->disableOriginalConstructor()
            ->getMock();

        $mockCampaign
            ->expects(self::once())
            ->method('getMinAmount')
            ->will($this->returnValue($minAmount));

        $campaignRepository = $this->getMockBuilder(\Pixelant\Crowdfunding\Domain\Repository\CampaignRepository::class)
            ->setMethods(['findByUid'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->inject($this->subject, 'campaignRepository', $campaignRepository);

        $campaignRepository
            ->expects(self::once())
            ->method('findByUid')
            ->with($campaignId)
            ->will($this->returnValue($mockCampaign));

        $result = $this->subject->_call('getChecksum', $campaignId, $pledgeId, $amount);
    }

    /**
     * @test
     * @expectedException \Exception
     */
    public function getChecksumForNonExistingCampaingThrowsException()
    {
        $campaignId = 15;
        $pledgeId = 0;
        $amount = 5;

        $campaignRepository = $this->getMockBuilder(\Pixelant\Crowdfunding\Domain\Repository\CampaignRepository::class)
            ->setMethods(['findByUid'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->inject($this->subject, 'campaignRepository', $campaignRepository);

        $campaignRepository
            ->expects(self::once())
            ->method('findByUid')
            ->with($campaignId)
            ->will($this->returnValue(null));

        $result = $this->subject->_call('getChecksum', $campaignId, $pledgeId, $amount);
    }

    /**
     * @test
     * @expectedException \Exception
     */
    public function getChecksumForCampaingAndPledgeWithWrongAmountThrowsException()
    {
        $campaignId = 15;
        $pledgeId = 20;
        $amount = 50;

        $mockPledge = $this->getMockBuilder(\Pixelant\Crowdfunding\Domain\Model\Pledging::class)
            ->setMethods(['getUid', 'getAmount'])
            ->disableOriginalConstructor()
            ->getMock();

        $mockPledge
            ->expects(self::once())
            ->method('getUid')
            ->will($this->returnValue($pledgeId));

        $mockPledge
            ->expects(self::once())
            ->method('getAmount')
            ->will($this->returnValue(123));

        $mockCampaign = $this->getMockBuilder(\Pixelant\Crowdfunding\Domain\Model\Campaign::class)
            ->setMethods(['getUid', 'getMinAmount', 'getCrdate', 'getPledges'])
            ->disableOriginalConstructor()
            ->getMock();

        $mockCampaign
            ->expects(self::once())
            ->method('getPledges')
            ->will($this->returnValue(['mockPledge' => $mockPledge]));

        $campaignRepository = $this->getMockBuilder(\Pixelant\Crowdfunding\Domain\Repository\CampaignRepository::class)
            ->setMethods(['findByUid'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->inject($this->subject, 'campaignRepository', $campaignRepository);

        $campaignRepository
            ->expects(self::once())
            ->method('findByUid')
            ->with($campaignId)
            ->will($this->returnValue($mockCampaign));

        $result = $this->subject->_call('getChecksum', $campaignId, $pledgeId, $amount);
    }

    /**
     * @test
     */
    public function getCheckRefererReturnsTrueWhenMatch()
    {

        $_SERVER['HTTPS'] = true;
        $_SERVER['SERVER_NAME'] = 'localhost';
        $_SERVER['HTTP_REFERER'] = 'https://localhost/somepage/';

        $result = $this->subject->_call('checkReferer');

        $this->assertEquals(true, $result);
    }

    /**
     * @test
     */
    public function getCheckRefererReturnsFalseWithWrongRefererHost()
    {

        $_SERVER['HTTPS'] = true;
        $_SERVER['SERVER_NAME'] = 'localhost';
        $_SERVER['HTTP_REFERER'] = 'https://remotehost/somepage/';

        $result = $this->subject->_call('checkReferer');

        $this->assertEquals(false, $result);
    }

    /**
     * @test
     */
    public function getCheckRefererReturnsFalseWithWrongProtocol()
    {

        $_SERVER['HTTPS'] = null;
        $_SERVER['SERVER_NAME'] = 'localhost';
        $_SERVER['HTTP_REFERER'] = 'https://localhost/somepage/';

        $result = $this->subject->_call('checkReferer');

        $this->assertEquals(false, $result);
    }

    /**
     * @test
     */
    public function getAjaxAction()
    {
        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'xmlhttprequest';
        $_SERVER['HTTPS'] = true;
        $_SERVER['SERVER_NAME'] = 'localhost';
        $_SERVER['HTTP_REFERER'] = 'https://localhost/somepage/';

        $mockRequest = $this->getMockBuilder(\TYPO3\CMS\Extbase\Mvc\Web\Request::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->inject($this->subject, 'request', $mockRequest);

        $result = $this->subject->_call('ajaxAction');
    }

    /**
     * @test
     */
    public function getAjaxActionReturnCode101WhenNotXmlHttpRequest()
    {
        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'notaxmlhttprequest';

        $mockRequest = $this->getMockBuilder(\TYPO3\CMS\Extbase\Mvc\Web\Request::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->inject($this->subject, 'request', $mockRequest);

        $result = json_decode($this->subject->_call('ajaxAction'), true);
        $this->assertEquals(0, $result['success']);
        $this->assertEquals(101, $result['code']);
    }

    /**
     * @test
     */
    public function getAjaxActionReturnCode102WithInvalidReferer()
    {
        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'xmlhttprequest';
        $_SERVER['HTTPS'] = true;
        $_SERVER['SERVER_NAME'] = 'localhost';
        $_SERVER['HTTP_REFERER'] = 'https://remotehost/somepage/';

        $mockRequest = $this->getMockBuilder(\TYPO3\CMS\Extbase\Mvc\Web\Request::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->inject($this->subject, 'request', $mockRequest);

        $result = json_decode($this->subject->_call('ajaxAction'), true);
        $this->assertEquals(0, $result['success']);
        $this->assertEquals(102, $result['code']);
    }

    /**
     * @test
     */
    public function getAjaxActionReturnCode103WithUnkownMethod()
    {
        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'xmlhttprequest';
        $_SERVER['HTTPS'] = true;
        $_SERVER['SERVER_NAME'] = 'localhost';
        $_SERVER['HTTP_REFERER'] = 'https://localhost/somepage/';
        $_POST['method'] = 'AnUnknownMethod';

        $mockRequest = $this->getMockBuilder(\TYPO3\CMS\Extbase\Mvc\Web\Request::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->inject($this->subject, 'request', $mockRequest);

        $result = json_decode($this->subject->_call('ajaxAction'), true);
        $this->assertEquals(0, $result['success']);
        $this->assertEquals(103, $result['code']);
    }

    /**
     * @test
     */
    public function getAjaxActionChecksum()
    {
        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'xmlhttprequest';
        $_SERVER['HTTPS'] = true;
        $_SERVER['SERVER_NAME'] = 'localhost';
        $_SERVER['HTTP_REFERER'] = 'https://localhost/somepage/';
        $_POST['method'] = 'checksum';
        $_POST['campaignId'] = 1;
        $_POST['pledgeId'] = 1;
        $_POST['amount'] = 100;
        $crdate = 112223334445;
        $checksum = hash(
            'sha256',
            $_POST['campaignId'] . '|' . $crdate . '|' . $_POST['pledgeId'] . '|' . $_POST['amount']
        );

        $mockPledge = $this->getMockBuilder(\Pixelant\Crowdfunding\Domain\Model\Pledging::class)
            ->setMethods(['getUid', 'getAmount'])
            ->disableOriginalConstructor()
            ->getMock();

        $mockPledge
            ->expects(self::once())
            ->method('getUid')
            ->will($this->returnValue($_POST['pledgeId']));

        $mockPledge
            ->expects(self::once())
            ->method('getAmount')
            ->will($this->returnValue($_POST['amount']));

        $mockCampaign = $this->getMockBuilder(\Pixelant\Crowdfunding\Domain\Model\Campaign::class)
            ->setMethods(['getUid', 'getMinAmount', 'getCrdate', 'getPledges'])
            ->disableOriginalConstructor()
            ->getMock();

        $mockCampaign
            ->expects(self::once())
            ->method('getPledges')
            ->will($this->returnValue(['mockPledge' => $mockPledge]));

        $mockCampaign
            ->expects(self::once())
            ->method('getCrdate')
            ->will($this->returnValue($crdate));

        $campaignRepository = $this->getMockBuilder(\Pixelant\Crowdfunding\Domain\Repository\CampaignRepository::class)
            ->setMethods(['findByUid'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->inject($this->subject, 'campaignRepository', $campaignRepository);

        $campaignRepository
            ->expects(self::once())
            ->method('findByUid')
            ->with($_POST['campaignId'])
            ->will($this->returnValue($mockCampaign));

        $mockRequest = $this->getMockBuilder(\TYPO3\CMS\Extbase\Mvc\Web\Request::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->inject($this->subject, 'request', $mockRequest);

        $result = json_decode($this->subject->_call('ajaxAction'), true);

        $this->assertEquals(1, $result['success']);
        $this->assertEquals($checksum, $result['message']);
    }

    /**
     * @test
     */
    public function getAjaxActionChecksumWillFail()
    {
        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'xmlhttprequest';
        $_SERVER['HTTPS'] = true;
        $_SERVER['SERVER_NAME'] = 'localhost';
        $_SERVER['HTTP_REFERER'] = 'https://localhost/somepage/';
        $_POST['method'] = 'checksum';
        $_POST['campaignId'] = 1;
        $_POST['pledgeId'] = 1;
        $_POST['amount'] = 100;
        $crdate = 112223334445;
        $checksum = hash(
            'sha256',
            $_POST['campaignId'] . '|' . $crdate . '|' . $_POST['pledgeId'] . '|' . $_POST['amount']
        );

        $mockPledge = $this->getMockBuilder(\Pixelant\Crowdfunding\Domain\Model\Pledging::class)
            ->setMethods(['getUid', 'getAmount'])
            ->disableOriginalConstructor()
            ->getMock();

        $mockPledge
            ->expects(self::once())
            ->method('getUid')
            ->will($this->returnValue($_POST['pledgeId']));

        $mockPledge
            ->expects(self::once())
            ->method('getAmount')
            ->will($this->returnValue(123));

        $mockCampaign = $this->getMockBuilder(\Pixelant\Crowdfunding\Domain\Model\Campaign::class)
            ->setMethods(['getUid', 'getMinAmount', 'getCrdate', 'getPledges'])
            ->disableOriginalConstructor()
            ->getMock();

        $mockCampaign
            ->expects(self::once())
            ->method('getPledges')
            ->will($this->returnValue(['mockPledge' => $mockPledge]));

        $campaignRepository = $this->getMockBuilder(\Pixelant\Crowdfunding\Domain\Repository\CampaignRepository::class)
            ->setMethods(['findByUid'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->inject($this->subject, 'campaignRepository', $campaignRepository);

        $campaignRepository
            ->expects(self::once())
            ->method('findByUid')
            ->with($_POST['campaignId'])
            ->will($this->returnValue($mockCampaign));

        $mockRequest = $this->getMockBuilder(\TYPO3\CMS\Extbase\Mvc\Web\Request::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->inject($this->subject, 'request', $mockRequest);

        $result = json_decode($this->subject->_call('ajaxAction'), true);

        $this->assertEquals(0, $result['success']);
    }
}
