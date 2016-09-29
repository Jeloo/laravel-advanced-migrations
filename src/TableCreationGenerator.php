<?php

namespace Jeloo\LaraMigrations;

class TableCreationGenerator implements GeneratorInterface
{
    /**
     * @var array
     */
    private $schema;

    public function __construct($table, array $schema)
    {
        $this->schema = $schema;
    }

    /**
     * @return string - The code to fill migration [up] method
     */
    public function generateUp()
    {
        $snippet = '';

        foreach ($this->schema as $col) {
            $snippet .= sprintf('$table->%s(\'%s\')', $col['type'], $col['name']);

            if (! empty($col['properties'])) {
                $snippet .= implode('->', $this->chainedStatements()).PHP_EOL;
                $snippet .= ';'.PHP_EOL;

                //@todo handle chained statements
            }
        }
    }

    protected function separateCallStatements()
    {
        //@todo implement
    }

    protected function chainedStatements()
    {
        //@todo implement
    }

    /**
     * @return string - The code to fill migration [down] method
     */
    public function generateDown()
    {
        return sprintf('$table->dropTable(%s);', 'table_name');
    }

}