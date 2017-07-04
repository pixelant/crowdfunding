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
class CampaignControllerTest extends \Nimut\TestingFramework\TestCase\FunctionalTestCase
{

    /**
     * @var array
     */
    protected $testExtensionsToLoad = array(
        'typo3conf/ext/crowdfunding',
    );

    /**
     * @test
     */
    public function loadPagesDatabaseFixtures()
    {
        $this->importDataSet('ntf://Database/pages.xml');
        $this->importDataSet('ntf://Database/pages_language_overlay.xml');
        $this->assertSame(7, $this->getDatabaseConnection()->exec_SELECTcountRows('*', 'pages'));
        $this->assertSame(2, $this->getDatabaseConnection()->exec_SELECTcountRows('*', 'pages_language_overlay'));
    }
}
