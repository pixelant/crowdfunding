<?php
defined('TYPO3_MODE') or die();

$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist']['crowdfunding_crowdfunding'] = 'pi_flexform';
// $TCA['tt_content']['types']['list']['subtypes_excludelist'][$pluginSignature] = 'code,layout,select_key,pages,recursive';
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue(
    'crowdfunding_crowdfunding',
    'FILE:EXT:crowdfunding/Configuration/FlexForms/flexform_crowdfunding.xml'
);
