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
                    'callChain' => 'unsigned',
                ]
            ],
        ],
        'down' => [
            ['call' => 'dropTable', 'of' => '$table']
        ],
    ],
];