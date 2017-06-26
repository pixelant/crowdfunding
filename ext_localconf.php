<?php
defined('TYPO3_MODE') || die('Access denied.');

call_user_func(
    function () {
        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
            'Pixelant.Crowdfunding',
            'Crowdfunding',
            [
                'Campaign' => 'list, show, checkout, charge, ajax'
            ],
            // non-cacheable actions
            [
                'Campaign' => 'checkout, charge, ajax'
            ]
        );

    // wizards
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig(
        'mod {
            wizards.newContentElement.wizardItems.plugins {
                elements {
                    crowdfunding {
                        icon = ' . \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath('crowdfunding') . 'Resources/Public/Icons/user_plugin_crowdfunding.svg
                        title = LLL:EXT:crowdfunding/Resources/Private/Language/locallang_db.xlf:tx_crowdfunding_domain_model_crowdfunding
                        description = LLL:EXT:crowdfunding/Resources/Private/Language/locallang_db.xlf:tx_crowdfunding_domain_model_crowdfunding.description
                        tt_content_defValues {
                            CType = list
                            list_type = crowdfunding_crowdfunding
                        }
                    }
                }
                show = *
            }
       }'
    );
    }
);
