<?php

namespace Jeloo\LaraMigrations;

class SchemaParserTest extends \PHPUnit_Framework_TestCase
{

    public function testParsesSchema()
    {
        $schemaParser = new \Jeloo\LaraMigrations\SchemaParser();

        $this->assertEquals(
            $schemaParser->parse([
                ['id', 'integer', 'unsigned', 'nullable'],
                ['email', 'string', 'nullable', 'unique'],
            ]),
            [
                ['name' => 'id', 'type' => 'integer', 'properties' => ['unsigned', 'nullable']],
                ['name' => 'email', 'type' => 'string', 'properties' => ['nullable', 'unique']]
            ]
        );
    }

    public function testGuessesTypes()
    {
        $schemaParser = new \Jeloo\LaraMigrations\SchemaParser();

        $this->assertEquals(
            $schemaParser->parse([
                ['id', 'unsigned', 'nullable'],
                ['email', 'nullable', 'unique'],
            ]),
            [
                ['name' => 'id', 'type' => 'integer', 'properties' => ['unsigned', 'nullable']],
                ['name' => 'email', 'type' => 'string', 'properties' => ['nullable', 'unique']]
            ]
        );
    }

}