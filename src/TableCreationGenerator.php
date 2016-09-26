<?php

namespace Jeloo\LaraMigrations;

class TableCreationGenerator
{
    /**
     * @var array
     */
    private $fields;

    public function __construct(array $fields)
    {
        $this->fields = $fields;
    }

    /**
     * @return string - The code to fill migration [up] method
     */
    public function generateUp()
    {

    }

    /**
     * @return string - The code to fill migration [down] method
     */
    public function generateDown()
    {

    }

}