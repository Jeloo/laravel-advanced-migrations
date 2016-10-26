<?php

namespace Jeloo\LaraMigrations;

use Illuminate\Config\Repository;

class MetaBasedGeneratorTest extends \PHPUnit_Framework_TestCase
{

    public function testGenerateUp()
    {
        $generator = new MetaBasedGenerator(
            [
                ['name' => 'id'],
                ['name' => 'title', 'type' => 'string'],
                ['name' => 'category_id', 'type' => 'integer', 'belongsTo' => 'categories']
            ],
            $this->provider()
        );

        $this->assertEquals(
            '$table->increments(\'id\');'.PHP_EOL.
            '$table->string(\'title\');'.PHP_EOL.
            '$table->integer(\'category_id\');'.PHP_EOL.
            '$table->foreign(\'category_id\')->references(\'id\')->on(\'categories\');'.PHP_EOL,
            $generator->generateUp()
        );
    }

    public function testGenerateDown()
    {
        $generator = new MetaBasedGenerator(
            [['name' => 'id']],
            $this->provider()
        );

        $this->assertEquals(
            '$table->dropTable();'.PHP_EOL,
            $generator->generateDown()
        );
    }

    public function testDoesNotReplacesColumnNamesAsPlaceholders()
    {
        $generator = new MetaBasedGenerator(
            [['name' => 'name', 'type' => 'string']],
            $this->provider()
        );

        $this->assertEquals(
            '$table->string(\'name\');'.PHP_EOL,
            $generator->generateUp()
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
                        'end'
                    ],
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
            ],
        ]);
    }

}