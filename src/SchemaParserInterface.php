<?php

namespace Jeloo\LaraMigrations;

interface SchemaParserInterface
{
    /**
     * Parse the args to clear structure
     * @return array - example result: ['name' => 'id', 'type' => 'integer', 'properties' => ['unique']]
     */
    public function parse();

}