<?php

use Jeloo\LaraMigrations\TableCreationGenerator;

class TableCreationGeneratorTest extends PHPUnit_Framework_TestCase
{

    public function testGenerateUp()
    {
        $generator = new TableCreationGenerator([
            ['id', 'integer', 'unsigned', 'nullable'],
            ['email', 'string', 'nullable', 'unique'],
        ]);

        $this->assertArraySubset($generator->generateUp(), [
            '$table->increments(\'id\')',
            '$table->string(\'email\')->nullable()->unique()'
        ]);
    }


}