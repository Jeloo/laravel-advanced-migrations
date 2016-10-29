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
                    'end'
                ]
            ],
            [
                'pattern' => ['name' => '/^(?!id)/'],
                'expressions' => [
                    'call' => '{type}',
                    'of' => '$table',
                    'withArgs' => '{name}',
                    'end'
                ],
            ],
            [
            'pattern' => ['name' => '/.+_id/'],
            'expressions' => [
                [
                    'call' => 'foreign',
                    'of' => '$table',
                    'withArgs' => '{name}'
                ],
                [
                    'callChain' => 'references',
                    'withArgs' => 'id'
                ],
                [
                    'callChain' => 'on',
                    'withArgs' => '{belongsTo}',
                    'end'
                ],
            ]
        ],
        ],
        'down' => [
            ['call' => 'dropTable', 'of' => '$table', 'end']
        ]
    ],
    'add' => [
        'up' => [
            'pattern' => ['name' => '_id'],
            'expressions' => [
                [
                    'call' => 'foreign',
                    'of' => '$table',
                    'withArgs' => '{name}',
                ],
                [
                    'callChain' => 'references',
                    'withArgs' => 'id',
                ],
                [
                    'callChain' => 'on',
                    'withArgs' => '{belongsTo}',
                ]
            ]
        ],
        'down' => [
            //@todo fill [down] meta
        ]
    ],
];