<?php

namespace Jeloo\LaraMigrations;

use \Mockery as m;
use Illuminate\Database\Schema\Builder;

class SchemaParserTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Builder
     */
    private $schemaBuilder;

    public function setUp()
    {
        parent::setUp();

        $this->schemaBuilder = m::mock(Builder::class)
            ->shouldReceive('hasTable')
            ->andReturn(true)
            ->getMock();
    }

    public function testParsesSchema()
    {
        $schemaParser = new \Jeloo\LaraMigrations\SchemaParser([
            ['id', 'integer', 'unsigned', 'nullable'],
            ['email', 'string', 'nullable', 'unique'],
        ], $this->schemaBuilder);

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
        ], $this->schemaBuilder);

        $this->assertEquals(
            $schemaParser->parse(),
            [
                ['name' => 'id', 'type' => 'integer', 'properties' => ['unsigned', 'nullable']],
                ['name' => 'email', 'type' => 'string', 'properties' => ['nullable', 'unique']]
            ]
        );
    }

    public function testGuessesRelatedTableName()
    {
        $schemaParser = new \Jeloo\LaraMigrations\SchemaParser([['category_id']], $this->schemaBuilder);

        $this->assertEquals(
            [
                ['name' => 'category_id', 'type' => 'integer', 'belongsTo' => 'category', 'properties' => [
                    'foreign',
                    'unsigned',
                    'nullable'
                ]],
            ],
            $schemaParser->parse()
        );
    }

}