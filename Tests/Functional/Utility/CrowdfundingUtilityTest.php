<?php
namespace Pixelant\Crowdfunding\Tests\Functional\Utility;

use \Pixelant\Crowdfunding\Utility\CrowdfundingUtility;

/**
 * Test case.
 */
class CrowdfundingUtilityTest extends \Nimut\TestingFramework\TestCase\FunctionalTestCase
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
    public function getSettings()
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

        $test = \Pixelant\Crowdfunding\Utility\CrowdfundingUtility::getSettings();
        $this->assertEquals('$', $test['currency']['currencySign']);
        $this->assertEquals('.', $test['currency']['decimalSeparator']);
        $this->assertEquals(',', $test['currency']['thousandsSeparator']);
        $this->assertEquals('1', $test['currency']['prependCurrency']);
        $this->assertEquals('1', $test['currency']['separateCurrency']);
        $this->assertEquals('0', $test['currency']['decimals']);
        $this->assertEquals('1234', $test['stripe']['secretKey']);
        $this->assertEquals('1234', $test['stripe']['publishableKey']);
        $this->assertEquals('FuncionalTestingOfCrowdfunding', $test['stripe']['name']);
        $this->assertEquals('', $test['stripe']['image']);
        $this->assertEquals('201706131', $test['ajaxPageType']);
        $this->assertEquals('', $test['adminEmail']);
    }

    /**
     * @test
     */
    public function getFormatCurrencySettingsAlt1()
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
                'Tests/Functional/Fixtures/TypoScript/currency_settings_alt1.ts'
            ]
        );

        $test = \Pixelant\Crowdfunding\Utility\CrowdfundingUtility::getSettings();
        $this->assertEquals(':-', $test['currency']['currencySign']);
        $this->assertEquals(',', $test['currency']['decimalSeparator']);
        $this->assertEquals('', $test['currency']['thousandsSeparator']);
        $this->assertEquals('0', $test['currency']['prependCurrency']);
        $this->assertEquals('0', $test['currency']['separateCurrency']);
        $this->assertEquals('0', $test['currency']['decimals']);
        $this->assertEquals('1234', $test['stripe']['secretKey']);
        $this->assertEquals('1234', $test['stripe']['publishableKey']);
        $this->assertEquals('FuncionalTestingOfCrowdfunding', $test['stripe']['name']);
        $this->assertEquals('', $test['stripe']['image']);
        $this->assertEquals('201706131', $test['ajaxPageType']);
        $this->assertEquals('', $test['adminEmail']);

        $this->assertEquals('0:-', CrowdfundingUtility::formatCurrency(.49));
        $this->assertEquals('1:-', CrowdfundingUtility::formatCurrency(.50));
        $this->assertEquals('12:-', CrowdfundingUtility::formatCurrency(12.49));
        $this->assertEquals('13:-', CrowdfundingUtility::formatCurrency(12.50));
        $this->assertEquals('123:-', CrowdfundingUtility::formatCurrency(123.444));
        $this->assertEquals('123:-', CrowdfundingUtility::formatCurrency(123.445));
        $this->assertEquals('1 234 568:-', CrowdfundingUtility::formatCurrency(1234567.89));

        $this->assertEquals('-0:-', CrowdfundingUtility::formatCurrency(-.49));
        $this->assertEquals('-1:-', CrowdfundingUtility::formatCurrency(-.50));
        $this->assertEquals('-12:-', CrowdfundingUtility::formatCurrency(-12.49));
        $this->assertEquals('-13:-', CrowdfundingUtility::formatCurrency(-12.50));
        $this->assertEquals('-123:-', CrowdfundingUtility::formatCurrency(-123.444));
        $this->assertEquals('-123:-', CrowdfundingUtility::formatCurrency(-123.445));
        $this->assertEquals('-1 234 568:-', CrowdfundingUtility::formatCurrency(-1234567.89));
    }

    /**
     * @test
     */
    public function getFormatCurrencySettingsAlt2()
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
                'Tests/Functional/Fixtures/TypoScript/currency_settings_alt2.ts'
            ]
        );

        $test = \Pixelant\Crowdfunding\Utility\CrowdfundingUtility::getSettings();
        $this->assertEquals('kr', $test['currency']['currencySign']);
        $this->assertEquals(',', $test['currency']['decimalSeparator']);
        $this->assertEquals('', $test['currency']['thousandsSeparator']);
        $this->assertEquals('0', $test['currency']['prependCurrency']);
        $this->assertEquals('1', $test['currency']['separateCurrency']);
        $this->assertEquals('2', $test['currency']['decimals']);
        $this->assertEquals('1234', $test['stripe']['secretKey']);
        $this->assertEquals('1234', $test['stripe']['publishableKey']);
        $this->assertEquals('FuncionalTestingOfCrowdfunding', $test['stripe']['name']);
        $this->assertEquals('', $test['stripe']['image']);
        $this->assertEquals('201706131', $test['ajaxPageType']);
        $this->assertEquals('', $test['adminEmail']);

        $this->assertEquals('0,49 kr', CrowdfundingUtility::formatCurrency(.49));
        $this->assertEquals('0,50 kr', CrowdfundingUtility::formatCurrency(.50));
        $this->assertEquals('12,49 kr', CrowdfundingUtility::formatCurrency(12.49));
        $this->assertEquals('12,50 kr', CrowdfundingUtility::formatCurrency(12.50));
        $this->assertEquals('123,44 kr', CrowdfundingUtility::formatCurrency(123.444));
        $this->assertEquals('123,45 kr', CrowdfundingUtility::formatCurrency(123.445));
        $this->assertEquals('1 234 567,89 kr', CrowdfundingUtility::formatCurrency(1234567.89));

        $this->assertEquals('-0,49 kr', CrowdfundingUtility::formatCurrency(-.49));
        $this->assertEquals('-0,50 kr', CrowdfundingUtility::formatCurrency(-.50));
        $this->assertEquals('-12,49 kr', CrowdfundingUtility::formatCurrency(-12.49));
        $this->assertEquals('-12,50 kr', CrowdfundingUtility::formatCurrency(-12.50));
        $this->assertEquals('-123,44 kr', CrowdfundingUtility::formatCurrency(-123.444));
        $this->assertEquals('-123,45 kr', CrowdfundingUtility::formatCurrency(-123.445));
        $this->assertEquals('-1 234 567,89 kr', CrowdfundingUtility::formatCurrency(-1234567.89));
    }

    /**
     * @test
     */
    public function getFormatCurrencySettingsAlt3()
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
                'Tests/Functional/Fixtures/TypoScript/currency_settings_alt3.ts'
            ]
        );

        $test = \Pixelant\Crowdfunding\Utility\CrowdfundingUtility::getSettings();
        $this->assertEquals('$', $test['currency']['currencySign']);
        $this->assertEquals('.', $test['currency']['decimalSeparator']);
        $this->assertEquals(',', $test['currency']['thousandsSeparator']);
        $this->assertEquals('1', $test['currency']['prependCurrency']);
        $this->assertEquals('0', $test['currency']['separateCurrency']);
        $this->assertEquals('0', $test['currency']['decimals']);
        $this->assertEquals('1234', $test['stripe']['secretKey']);
        $this->assertEquals('1234', $test['stripe']['publishableKey']);
        $this->assertEquals('FuncionalTestingOfCrowdfunding', $test['stripe']['name']);
        $this->assertEquals('', $test['stripe']['image']);
        $this->assertEquals('201706131', $test['ajaxPageType']);
        $this->assertEquals('', $test['adminEmail']);

        $this->assertEquals('$0', CrowdfundingUtility::formatCurrency(.49));
        $this->assertEquals('$1', CrowdfundingUtility::formatCurrency(.50));
        $this->assertEquals('$12', CrowdfundingUtility::formatCurrency(12.49));
        $this->assertEquals('$13', CrowdfundingUtility::formatCurrency(12.50));
        $this->assertEquals('$123', CrowdfundingUtility::formatCurrency(123.444));
        $this->assertEquals('$123', CrowdfundingUtility::formatCurrency(123.445));
        $this->assertEquals('$1,234,568', CrowdfundingUtility::formatCurrency(1234567.89));

        $this->assertEquals('$-0', CrowdfundingUtility::formatCurrency(-.49));
        $this->assertEquals('$-1', CrowdfundingUtility::formatCurrency(-.50));
        $this->assertEquals('$-12', CrowdfundingUtility::formatCurrency(-12.49));
        $this->assertEquals('$-13', CrowdfundingUtility::formatCurrency(-12.50));
        $this->assertEquals('$-123', CrowdfundingUtility::formatCurrency(-123.444));
        $this->assertEquals('$-123', CrowdfundingUtility::formatCurrency(-123.445));
        $this->assertEquals('$-1,234,568', CrowdfundingUtility::formatCurrency(-1234567.89));
    }

    /**
     * @test
     */
    public function getFormatCurrencySettingsAlt4()
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
                'Tests/Functional/Fixtures/TypoScript/currency_settings_alt4.ts'
            ]
        );

        $test = \Pixelant\Crowdfunding\Utility\CrowdfundingUtility::getSettings();
        $this->assertEquals('$', $test['currency']['currencySign']);
        $this->assertEquals('.', $test['currency']['decimalSeparator']);
        $this->assertEquals(',', $test['currency']['thousandsSeparator']);
        $this->assertEquals('1', $test['currency']['prependCurrency']);
        $this->assertEquals('1', $test['currency']['separateCurrency']);
        $this->assertEquals('2', $test['currency']['decimals']);
        $this->assertEquals('1234', $test['stripe']['secretKey']);
        $this->assertEquals('1234', $test['stripe']['publishableKey']);
        $this->assertEquals('FuncionalTestingOfCrowdfunding', $test['stripe']['name']);
        $this->assertEquals('', $test['stripe']['image']);
        $this->assertEquals('201706131', $test['ajaxPageType']);
        $this->assertEquals('', $test['adminEmail']);

        $this->assertEquals('$ 0.49', CrowdfundingUtility::formatCurrency(.49));
        $this->assertEquals('$ 0.50', CrowdfundingUtility::formatCurrency(.50));
        $this->assertEquals('$ 12.49', CrowdfundingUtility::formatCurrency(12.49));
        $this->assertEquals('$ 12.50', CrowdfundingUtility::formatCurrency(12.50));
        $this->assertEquals('$ 123.44', CrowdfundingUtility::formatCurrency(123.444));
        $this->assertEquals('$ 123.45', CrowdfundingUtility::formatCurrency(123.445));
        $this->assertEquals('$ 1,234,567.89', CrowdfundingUtility::formatCurrency(1234567.89));

        $this->assertEquals('$ -0.49', CrowdfundingUtility::formatCurrency(-.49));
        $this->assertEquals('$ -0.50', CrowdfundingUtility::formatCurrency(-.50));
        $this->assertEquals('$ -12.49', CrowdfundingUtility::formatCurrency(-12.49));
        $this->assertEquals('$ -12.50', CrowdfundingUtility::formatCurrency(-12.50));
        $this->assertEquals('$ -123.44', CrowdfundingUtility::formatCurrency(-123.444));
        $this->assertEquals('$ -123.45', CrowdfundingUtility::formatCurrency(-123.445));
        $this->assertEquals('$ -1,234,567.89', CrowdfundingUtility::formatCurrency(-1234567.89));
    }
}
