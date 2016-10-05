<?php

use Jeloo\LaraMigrations\TableCreationGenerator;

class TableCreationGeneratorTest extends PHPUnit_Framework_TestCase
{

    public function testGenerateUp()
    {
        $generator = new TableCreationGenerator('', [
            ['name' => 'id']
        ]);

        $this->assertEquals(
            $generator->generateUp(),
            '$table->increments(\'id\')'
        );
    }



}