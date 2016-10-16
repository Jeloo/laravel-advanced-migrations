<?php

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