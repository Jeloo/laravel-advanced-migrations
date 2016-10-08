<?php

use Jeloo\LaraMigrations\MetaBasedGenerator;

class MetaBasedGeneratorTest extends PHPUnit_Framework_TestCase
{

    public function testGenerateUp()
    {
        $generator = new MetaBasedGenerator(
            '',
            [['name' => 'id']],
            $this->provider()
        );

        $this->assertEquals(
            $generator->generateUp(),
            '$table->increments(\'id\');'.PHP_EOL
        );
    }

    public function testGenerateDown()
    {
        $generator = new MetaBasedGenerator(
            '',
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
        return [
            'up' => [
                [
                    'pattern' => ['name' => 'id'],
                    'actions' => ['call' => 'increments', 'of' => '$table', 'withArgs' => 'id']
                ],
            ],
            'down' => [
                ['call' => 'dropTable', 'of' => '$table']
            ],
        ];
    }

}