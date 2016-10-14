<?php

namespace Jeloo\LaraMigrations;

interface SchemaParserInterface
{
    /**
     * Parse the args to clear structure
     * @param array $input
     * @return array - example result: ['name' => 'id', 'type' => 'integer', 'properties' => ['unique']]
     */
    public function parse(array $input);

}