<?php

namespace Jeloo\LaraMigrations;

use Jeloo\LaraMigrations\MigrationNameParser as Parser;
use PHPUnit_Framework_TestCase as TestCase;

class MigrationNameParserTest extends TestCase
{

    public function testParseVerb()
    {
        $parser = new Parser('create_users_table');
        $this->assertEquals($parser->parseVerb(), 'create');

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid verb: foo. Allowed verbs: create, add, drop');

        $parser = new Parser('foo_users_table');
        $parser->parseVerb();
    }

    public function testParseTableName()
    {
        $parser = new Parser('create_users_table');
        $this->assertEquals($parser->parseTableName(), 'users');

        $parser = new Parser('drop_users');
        $this->assertEquals($parser->parseTableName(), 'users');

        $this->expectException(\InvalidArgumentException::class);
        $parser = new Parser('drop');
        $parser->parseTableName();
    }

}