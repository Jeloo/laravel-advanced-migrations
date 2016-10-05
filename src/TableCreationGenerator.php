<?php

namespace Jeloo\LaraMigrations;

class TableCreationGenerator extends AbstractGenerator
{
    /**
     * @var array
     */
    private $schema;

    /**
     * @var array
     */
    private $meta = [
        [
            'pattern' => ['name' => 'id'],
            'actions' => ['call' => 'increments', 'of' => '$table', 'withArgs' => 'id']
        ]
    ];

    public function __construct($table, array $schema)
    {
        $this->schema = $schema;
    }

    /**
     * @return string - The code to fill migration [up] method
     */
    public function generateUp()
    {
        foreach ($this->schema as $column) {
            $actions = $this->getActionsByColumnSchema($column);
            $this->generateByActionsMeta($actions);
        }

        return $this->output;
    }

    /**
     * @return string - The code to fill migration [down] method
     */
    public function generateDown()
    {
        return sprintf('$table->dropTable(%s);', 'table_name');
    }

    /**
     * @param array $column
     * @return array
     */
    private function getActionsByColumnSchema(array $column)
    {
        foreach ($this->meta as $m) {
            if (! empty(array_intersect($column, $m['pattern']))) {
                return $m['actions'];
            }
        }

        return [];
    }

    /**
     * @param array $actions
     */
    private function generateByActionsMeta(array $actions)
    {
        if (is_array(array_values($actions)[0])) {
            // if array is not flat then generate code recursively
            $this->generateByActionsMeta($actions);
        } else {
            // call methods from parent to generate code
            foreach ($actions as $method => $arg) {
                $this->$method($arg);
            }
        }

    }

}