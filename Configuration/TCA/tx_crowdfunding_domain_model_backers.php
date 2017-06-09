<?php
return [
    'ctrl' => [
        'title' => 'LLL:EXT:crowdfunding/Resources/Private/Language/locallang_db.xlf:tx_crowdfunding_domain_model_backers',
        'label' => 'campaign_id',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'versioningWS' => true,
        'languageField' => 'sys_language_uid',
        'transOrigPointerField' => 'l10n_parent',
        'transOrigDiffSourceField' => 'l10n_diffsource',
        'delete' => 'deleted',
        'enablecolumns' => [
            'disabled' => 'hidden',
            'starttime' => 'starttime',
            'endtime' => 'endtime',
        ],
        'searchFields' => 'campaign_id,stage_id,frontend_user_id,reference,amount,status',
        'iconfile' => 'EXT:crowdfunding/Resources/Public/Icons/tx_crowdfunding_domain_model_backers.gif'
    ],
    'interface' => [
        'showRecordFieldList' => 'sys_language_uid, l10n_parent, l10n_diffsource, hidden, campaign_id, stage_id, frontend_user_id, reference, amount, status',
    ],
    'types' => [
        '1' => ['showitem' => 'sys_language_uid, l10n_parent, l10n_diffsource, hidden, campaign_id, stage_id, frontend_user_id, reference, amount, status, --div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.access, starttime, endtime'],
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
                'foreign_table' => 'tx_crowdfunding_domain_model_backers',
                'foreign_table_where' => 'AND tx_crowdfunding_domain_model_backers.pid=###CURRENT_PID### AND tx_crowdfunding_domain_model_backers.sys_language_uid IN (-1,0)',
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
        'hidden' => [
            'exclude' => true,
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.hidden',
            'config' => [
                'type' => 'check',
                'items' => [
                    '1' => [
                        '0' => 'LLL:EXT:lang/locallang_core.xlf:labels.enabled'
                    ]
                ],
            ],
        ],
        'starttime' => [
            'exclude' => true,
            'l10n_mode' => 'mergeIfNotBlank',
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.starttime',
            'config' => [
                'type' => 'input',
                'size' => 13,
                'eval' => 'datetime',
                'default' => 0,
            ],
        ],
        'endtime' => [
            'exclude' => true,
            'l10n_mode' => 'mergeIfNotBlank',
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.endtime',
            'config' => [
                'type' => 'input',
                'size' => 13,
                'eval' => 'datetime',
                'default' => 0,
                'range' => [
                    'upper' => mktime(0, 0, 0, 1, 1, 2038)
                ],
            ],
        ],

        'campaign_id' => [
            'exclude' => false,
            'label' => 'LLL:EXT:crowdfunding/Resources/Private/Language/locallang_db.xlf:tx_crowdfunding_domain_model_backers.campaign_id',
            'config' => [
                'type' => 'input',
                'size' => 4,
                'eval' => 'int,required'
            ]
        ],
        'stage_id' => [
            'exclude' => false,
            'label' => 'LLL:EXT:crowdfunding/Resources/Private/Language/locallang_db.xlf:tx_crowdfunding_domain_model_backers.stage_id',
            'config' => [
                'type' => 'input',
                'size' => 4,
                'eval' => 'int'
            ]
        ],
        'frontend_user_id' => [
            'exclude' => false,
            'label' => 'LLL:EXT:crowdfunding/Resources/Private/Language/locallang_db.xlf:tx_crowdfunding_domain_model_backers.frontend_user_id',
            'config' => [
                'type' => 'input',
                'size' => 4,
                'eval' => 'int'
            ]
        ],
        'reference' => [
            'exclude' => false,
            'label' => 'LLL:EXT:crowdfunding/Resources/Private/Language/locallang_db.xlf:tx_crowdfunding_domain_model_backers.reference',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim,required'
            ],
        ],
        'amount' => [
            'exclude' => false,
            'label' => 'LLL:EXT:crowdfunding/Resources/Private/Language/locallang_db.xlf:tx_crowdfunding_domain_model_backers.amount',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'double2,required'
            ]
        ],
        'status' => [
            'exclude' => false,
            'label' => 'LLL:EXT:crowdfunding/Resources/Private/Language/locallang_db.xlf:tx_crowdfunding_domain_model_backers.status',
            'config' => [
                'type' => 'input',
                'size' => 4,
                'eval' => 'int'
            ]
        ],
    
    ],
];
