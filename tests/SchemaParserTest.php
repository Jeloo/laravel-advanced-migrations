<?php

namespace Jeloo\LaraMigrations;

class SchemaParserTest extends \PHPUnit_Framework_TestCase
{

    public function testParsesSchema()
    {
        $schemaParser = new \Jeloo\LaraMigrations\SchemaParser([
            ['id', 'integer', 'unsigned', 'nullable'],
            ['email', 'string', 'nullable', 'unique'],
        ]);

        $this->assertEquals(
            $schemaParser->parse(),
            [
                ['name' => 'id', 'type' => 'integer', 'properties' => ['unsigned', 'nullable']],
                ['name' => 'email', 'type' => 'string', 'properties' => ['nullable', 'unique']]
            ]
        );
    }

    public function testGuessesTypes()
    {
        $schemaParser = new \Jeloo\LaraMigrations\SchemaParser([
            ['id', 'unsigned', 'nullable'],
            ['email', 'nullable', 'unique'],
        ]);

        $this->assertEquals(
            $schemaParser->parse(),
            [
                ['name' => 'id', 'type' => 'integer', 'properties' => ['unsigned', 'nullable']],
                ['name' => 'email', 'type' => 'string', 'properties' => ['nullable', 'unique']]
            ]
        );
    }

}