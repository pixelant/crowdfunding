<?php
return [
    'ctrl' => [
        'title' => 'LLL:EXT:crowdfunding/Resources/Private/Language/locallang_db.xlf:tx_crowdfunding_domain_model_backer',
        'label' => 'email',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'versioningWS' => true,
        'languageField' => 'sys_language_uid',
        'transOrigPointerField' => 'l10n_parent',
        'transOrigDiffSourceField' => 'l10n_diffsource',
        'enablecolumns' => [
        ],
        'searchFields' => 'email,name,transactions',
        'iconfile' => 'EXT:crowdfunding/Resources/Public/Icons/tx_crowdfunding_domain_model_backer.gif'
    ],
    'interface' => [
        'showRecordFieldList' => 'sys_language_uid, l10n_parent, l10n_diffsource, email, name, transactions',
    ],
    'types' => [
        '1' => ['showitem' => 'sys_language_uid, l10n_parent, l10n_diffsource, email, name, transactions'],
    ],
    'columns' => [
        'sys_language_uid' => [
            'exclude' => true,
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.language',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'special' => 'languages',
                'items' => [
                    [
                        'LLL:EXT:lang/locallang_general.xlf:LGL.allLanguages',
                        -1,
                        'flags-multiple'
                    ]
                ],
                'default' => 0,
            ],
        ],
        'l10n_parent' => [
            'displayCond' => 'FIELD:sys_language_uid:>:0',
            'exclude' => true,
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.l18n_parent',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['', 0],
                ],
                'foreign_table' => 'tx_crowdfunding_domain_model_backer',
                'foreign_table_where' => 'AND tx_crowdfunding_domain_model_backer.pid=###CURRENT_PID### AND tx_crowdfunding_domain_model_backer.sys_language_uid IN (-1,0)',
            ],
        ],
        'l10n_diffsource' => [
            'config' => [
                'type' => 'passthrough',
            ],
        ],
        't3ver_label' => [
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.versionLabel',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'max' => 255,
            ],
        ],

        'email' => [
            'exclude' => false,
            'label' => 'LLL:EXT:crowdfunding/Resources/Private/Language/locallang_db.xlf:tx_crowdfunding_domain_model_backer.email',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim,required'
            ],
        ],
        'name' => [
            'exclude' => false,
            'label' => 'LLL:EXT:crowdfunding/Resources/Private/Language/locallang_db.xlf:tx_crowdfunding_domain_model_backer.name',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim,required'
            ],
        ],
        'transactions' => [
            'exclude' => true,
            'label' => 'LLL:EXT:crowdfunding/Resources/Private/Language/locallang_db.xlf:tx_crowdfunding_domain_model_backer.transactions',
            'config' => [
                'type' => 'inline',
                'foreign_table' => 'tx_crowdfunding_domain_model_transaction',
                'foreign_field' => 'backer',
                'maxitems' => 9999,
                'appearance' => [
                    'collapseAll' => 0,
                    'levelLinksPosition' => 'top',
                    'showSynchronizationLink' => 1,
                    'showPossibleLocalizationRecords' => 1,
                    'showAllLocalizationLink' => 1
                ],
            ],

        ],
    
    ],
];
