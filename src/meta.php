<?php

// Meta that determines the list of functions that will be called on generated code.
// The keys are the verbs and the values are the list of explanations how to generate the code.
return [
    'create' => [
        'up' => [
            [
                'pattern' => ['name' => 'id'],
                'expressions' => [
                    'call' => 'increments',
                    'of' => '$table',
                    'withArgs' => 'id',
                ]
            ],
            [
                'expressions' => [
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
            'expressions' => [
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