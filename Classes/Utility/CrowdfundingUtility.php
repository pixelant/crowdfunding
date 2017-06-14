<?php

namespace Pixelant\Crowdfunding\Utility;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;
use TYPO3\CMS\Extbase\Object\ObjectManager;

class CrowdfundingUtility
{
    /**
     * Get module settings
     *
     * @return array
     */
    public static function getSettings()
    {
        $objectManager = GeneralUtility::makeInstance(
            ObjectManager::class
        );
        $configurationManager = $objectManager->get(
            ConfigurationManager::class
        );
        $typoScriptSetupFull = $configurationManager->getConfiguration(
            ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT
        );
        $pluginName = 'tx_crowdfunding_crowdfunding';
        if ($typoScriptSetupFull['plugin.'][$pluginName . '.']['settings.']) {
            $typoScript = GeneralUtility::removeDotsFromTS(
                $typoScriptSetupFull['plugin.'][$pluginName . '.']['settings.']
            );
        }
        return $typoScript;
    }

    /**
     * Get float value to "currency" string
     *
     * @param float $amount
     *
     * @return string
     */
    public static function formatCurrency(float $amount)
    {
        $settings = self::getSettings();
        $currencySettings = $settings['currency'];
        
        $currencySign = $currencySettings['currencySign'];
        $decimalSeparator = $currencySettings['decimalSeparator'];
        $thousandsSeparator = $currencySettings['thousandsSeparator'];
        $prependCurrency = $currencySettings['prependCurrency'];
        $separateCurrency = $currencySettings['separateCurrency'];
        $decimals = $currencySettings['decimals'];

        if (empty($thousandsSeparator)) {
            $thousandsSeparator = ' ';
        }

        if (empty($amount)) {
            $amount = 0.0;
        } else {
            $amount = (float)$amount;
        }
        $output = number_format($amount, $decimals, $decimalSeparator, $thousandsSeparator);
        if ($currencySign !== '') {
            $currencySeparator = $separateCurrency ? ' ' : '';
            if ($prependCurrency === true) {
                $output = $currencySign . $currencySeparator . $output;
            } else {
                $output = $output . $currencySeparator . $currencySign;
            }
        }
        return $output;
    }
}
