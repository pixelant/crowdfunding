<?php
return [
    'ctrl' => [
        'title' => 'LLL:EXT:crowdfunding/Resources/Private/Language/locallang_db.xlf:tx_crowdfunding_domain_model_transaction',
        'label' => 'reference',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'versioningWS' => true,
        'languageField' => 'sys_language_uid',
        'transOrigPointerField' => 'l10n_parent',
        'transOrigDiffSourceField' => 'l10n_diffsource',
        'enablecolumns' => [
        ],
        'searchFields' => 'reference,amount,pledging_id,state,status',
        'iconfile' => 'EXT:crowdfunding/Resources/Public/Icons/tx_crowdfunding_domain_model_transaction.gif'
    ],
    'interface' => [
        'showRecordFieldList' => 'sys_language_uid, l10n_parent, l10n_diffsource, reference, amount, pledging_id, state, status',
    ],
    'types' => [
        '1' => ['showitem' => 'sys_language_uid, l10n_parent, l10n_diffsource, reference, amount, pledging_id, state, status'],
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
                'foreign_table' => 'tx_crowdfunding_domain_model_transaction',
                'foreign_table_where' => 'AND tx_crowdfunding_domain_model_transaction.pid=###CURRENT_PID### AND tx_crowdfunding_domain_model_transaction.sys_language_uid IN (-1,0)',
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

        'reference' => [
            'exclude' => false,
            'label' => 'LLL:EXT:crowdfunding/Resources/Private/Language/locallang_db.xlf:tx_crowdfunding_domain_model_transaction.reference',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim,required'
            ],
        ],
        'amount' => [
            'exclude' => false,
            'label' => 'LLL:EXT:crowdfunding/Resources/Private/Language/locallang_db.xlf:tx_crowdfunding_domain_model_transaction.amount',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'double2,required'
            ]
        ],
        'pledging_id' => [
            'exclude' => false,
            'label' => 'LLL:EXT:crowdfunding/Resources/Private/Language/locallang_db.xlf:tx_crowdfunding_domain_model_transaction.pledging_id',
            'config' => [
                'type' => 'input',
                'size' => 4,
                'eval' => 'int'
            ]
        ],
        'state' => [
            'exclude' => false,
            'label' => 'LLL:EXT:crowdfunding/Resources/Private/Language/locallang_db.xlf:tx_crowdfunding_domain_model_transaction.state',
            'config' => [
                'type' => 'input',
                'size' => 4,
                'eval' => 'int,required'
            ]
        ],
        'status' => [
            'exclude' => false,
            'label' => 'LLL:EXT:crowdfunding/Resources/Private/Language/locallang_db.xlf:tx_crowdfunding_domain_model_transaction.status',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim,required'
            ],
        ],
    
        'backer' => [
            'config' => [
                'type' => 'passthrough',
            ],
        ],
    ],
];
