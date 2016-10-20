<?php

namespace Jeloo\LaraMigrations;

use Illuminate\Config\Repository;

class MetaBasedGeneratorTest extends \PHPUnit_Framework_TestCase
{

    public function testGenerateUp()
    {
        $generator = new MetaBasedGenerator(
            [['name' => 'id'], ['name' => 'title', 'type' => 'string']],
            $this->provider()
        );

        $this->assertEquals(
            $generator->generateUp(),
            '$table->increments(\'id\')->unsigned();'.PHP_EOL.
            '$table->string(\'title\');'.PHP_EOL
        );
    }

    public function testGenerateDown()
    {
        $generator = new MetaBasedGenerator(
            [['name' => 'id']],
            $this->provider()
        );

        $this->assertEquals(
            $generator->generateDown(),
            '$table->dropTable();'.PHP_EOL
        );
    }

    private function provider()
    {
        return new Repository([
            'up' => [
                [
                    'pattern' => ['name' => 'id'],
                    'expressions' => [
                        'call' => 'increments',
                        'of' => '$table',
                        'withArgs' => 'id',
                        'callChain' => 'unsigned',
                    ]
                ],
                [
                    'expressions' => [
                        'call' => 'type',
                        'of' => '$table',
                        'withArgs' => 'name'
                    ],
                ]
            ],
            'down' => [
                ['call' => 'dropTable', 'of' => '$table']
            ],
        ]);
    }

}