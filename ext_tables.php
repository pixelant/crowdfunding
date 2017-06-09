<?php
defined('TYPO3_MODE') || die('Access denied.');

call_user_func(
    function()
    {

        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
            'Pixelant.Crowdfunding',
            'Crowdfunding',
            'Crowdfunding'
        );

        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile('crowdfunding', 'Configuration/TypoScript', 'Crowdfunding');

        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_crowdfunding_domain_model_campaign', 'EXT:crowdfunding/Resources/Private/Language/locallang_csh_tx_crowdfunding_domain_model_campaign.xlf');
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_crowdfunding_domain_model_campaign');

        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_crowdfunding_domain_model_stage', 'EXT:crowdfunding/Resources/Private/Language/locallang_csh_tx_crowdfunding_domain_model_stage.xlf');
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_crowdfunding_domain_model_stage');

        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_crowdfunding_domain_model_goal', 'EXT:crowdfunding/Resources/Private/Language/locallang_csh_tx_crowdfunding_domain_model_goal.xlf');
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_crowdfunding_domain_model_goal');

        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::makeCategorizable(
            crowdfunding,
            'tx_crowdfunding_domain_model_campaign'
        );

    }
);
