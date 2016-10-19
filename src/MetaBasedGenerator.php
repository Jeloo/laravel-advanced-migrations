<?php

namespace Jeloo\LaraMigrations;

use Illuminate\Contracts\Config\Repository as Meta;

class MetaBasedGenerator extends AbstractGenerator
{
    /**
     * @var array
     */
    private $schema;

    /**
     * @var Meta
     */
    private $meta;

    /**
     * MetaGenerator constructor.
     * @param $table
     * @param array $schema
     * @param array $meta
     * @throws \InvalidArgumentException
     */
    public function __construct(array $schema, Meta $meta)
    {
        $this->schema = $schema;

        if (! $meta->has('up') || ! $meta->has('down')) {
            throw new \InvalidArgumentException('Meta must contain both [up] and [down] sub arrays');
        }

        $this->meta = $meta;
    }

    /**
     * @return string - The code to fill migration [up] method
     */
    public function generateUp()
    {
        return $this->generate();
    }

    /**
     * @return string - The code to fill migration [down] method
     */
    public function generateDown()
    {
        return $this->generate('down');
    }

    /**
     * @param string $migrationMethod
     * @return string - Complete output code
     */
    protected function generate($migrationMethod = 'up')
    {
        $this->handleTable($migrationMethod);
        $this->handleColumns($migrationMethod);
        $this->endStatement();
        return $this->output;
    }

    /**
     * @param string $migrationMethod
     */
    final private function handleTable($migrationMethod)
    {
        foreach ($this->meta[$migrationMethod] as $m) {
            if (! array_key_exists('actions', $m)) {
                $this->generateByMeta($m);
            }
        }
    }

    /**
     * @param string $migrationMethod
     */
    final private function handleColumns($migrationMethod)
    {
        foreach ($this->schema as $column) {
            $patternActions = $this->getActionsByColumnSchema($column, $this->meta[$migrationMethod]);
            $placeholderActions = $this->getActionsFillingPlaceholders($column, $this->meta[$migrationMethod]);
            $actions = array_merge($patternActions, $placeholderActions);

            if (! empty($actions)) {
                $this->generateByMeta($actions);
            }
        }
    }

    /**
     * @param array $column
     * @return array
     */
    final private function getActionsByColumnSchema(array $column, array $meta)
    {
        foreach ($meta as $m) {
            if (
                array_key_exists('pattern', $m) &&
                ! empty(array_intersect($column, $m['pattern']))
            ) {
                return $m['actions'];
            }
        }

        return [];
    }

    final private function getActionsFillingPlaceholders(array $columns, array $meta)
    {
        $actionsWithPlaceholders = array_filter($meta, function ($actionPredicate) {
            return in_array(preg_replace('/[{}]/', '', $actionPredicate), ['name', 'type']);
        });

        return array_map(function ($col) use ($actionsWithPlaceholders) {
            //@todo fill actions with placeholders
        }, $columns);
    }

    /**
     * @param array $instructions
     */
    final private function generateByMeta(array $instructions)
    {
        if (is_array(array_values($instructions)[0])) {
            // if array is not flat then generate code recursively
            $this->generateByMeta($instructions);
        } else {
            // call methods from parent to generate code
            foreach ($instructions as $method => $arg) {
                $this->$method($arg);
            }
        }

    }

}