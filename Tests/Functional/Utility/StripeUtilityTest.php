<?php
namespace Pixelant\Crowdfunding\Tests\Functional\Utility;

use \Pixelant\Crowdfunding\Utility\StripeUtility;

/**
 * Test case.
 */
class StripeUtilityTest extends \Nimut\TestingFramework\TestCase\FunctionalTestCase
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
    public function createCustomer()
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
                'Tests/Functional/Fixtures/TypoScript/crowdfunding.ts',
                'Tests/Functional/Fixtures/TypoScript/stripe_api_keys.ts'
            ]
        );

        $test = \Pixelant\Crowdfunding\Utility\CrowdfundingUtility::getSettings();
        var_dump($test);
    }
}
