<?php

use Jeloo\LaraMigrations\TableCreationGenerator;

class SchemaParserTest extends PHPUnit_Framework_TestCase
{

    public function testParse()
    {
        $schemaParser = new \Jeloo\LaraMigrations\SchemaParser([
            ['id', 'integer', 'unsigned', 'nullable'],
            ['email', 'string', 'nullable', 'unique'],
        ]);

        $this->assertEquals($schemaParser->parse(), [
            ['name' => 'id', 'type' => 'integer', 'properties' => ['unsigned', 'nullable']],
            ['name' => 'email', 'type' => 'string', 'properties' => ['nullable', 'unique']]
        ]);
    }



}