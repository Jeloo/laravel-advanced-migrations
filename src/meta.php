<?php

// Meta that determines the list of functions that will be called on generated code.
// The keys are the verbs and the values are the list of explanations how to generate the code.
return [
    'create' => [
        'up' => [
            [
                'pattern' => ['name' => 'id'],
                'actions' => [
                    'call' => 'increments',
                    'of' => '$table',
                    'withArgs' => 'id',
                ]
            ],
            [
                'actions' => [
                    'call' => 'type',
                    'of' => '$table'
                ],
            ]
        ],
        'down' => [
            ['call' => 'dropTable', 'of' => '$table']
        ]
    ],
    'add' => [
        'up' => [
            'pattern' => ['name' => '.+_id'],
            'actions' => [
                [
                    'call' => 'foreign',
                    'of' => '$table',
                    'withArgs' => '{column}',
                ],
                [
                    'callChain' => 'references',
                    'withArgs' => 'id',
                ],
                [
                    'callChain' => 'on',
                    'withArgs' => '{parent table}',
                ]
            ]
        ],
        'down' => [
            //@todo fill [down] meta
        ]
    ],
];