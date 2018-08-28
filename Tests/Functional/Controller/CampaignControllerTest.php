<?php
namespace Pixelant\Crowdfunding\Tests\Functional\Controller;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;
use TYPO3\CMS\Extbase\Object\ObjectManager;

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

    protected function setUp()
    {
        parent::setUp();
    }

    protected function tearDown()
    {
        parent::tearDown();
    }

    /**
     * @test
     */
    public function loadPagesDatabaseFixtures()
    {
        // First import some page records
        $this->importDataSet('ntf://Database/pages.xml');

        // Import tt_content record that should be shown on your home page
        $this->importDataSet('ntf://Database/tt_content.xml');

        // Setup the page with uid 1 and include the TypoScript as sys_template record
        $this->setUpFrontendRootPage(
            1,
            [
                'ntf://TypoScript/JsonRenderer.ts',
                'Tests/Functional/Fixtures/TypoScript/crowdfunding.ts'
            ]
        );

        $this->importDataSet('Tests/Functional/Fixtures/Database/tx_crowdfunding_domain_model_backer.xml');
        $this->importDataSet('Tests/Functional/Fixtures/Database/tx_crowdfunding_domain_model_campaign.xml');
        $this->importDataSet('Tests/Functional/Fixtures/Database/tx_crowdfunding_domain_model_pledging.xml');
        $this->importDataSet('Tests/Functional/Fixtures/Database/tx_crowdfunding_domain_model_goal.xml');

        $this->assertSame(1, $this->getDatabaseConnection()->exec_SELECTcountRows('*', 'tx_crowdfunding_domain_model_backer'));
        $this->assertSame(2, $this->getDatabaseConnection()->exec_SELECTcountRows('*', 'tx_crowdfunding_domain_model_campaign'));
        $this->assertSame(2, $this->getDatabaseConnection()->exec_SELECTcountRows('*', 'tx_crowdfunding_domain_model_pledging'));
        $this->assertSame(2, $this->getDatabaseConnection()->exec_SELECTcountRows('*', 'tx_crowdfunding_domain_model_goal'));
    }
}
