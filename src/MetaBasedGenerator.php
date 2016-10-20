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
     * @var array
     */
    private $placeholdersExclude = ['id'];

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
            $patternExpressions = $this->getExpressionsByColumn($column, $this->meta[$migrationMethod]);
            $placeholderExpressions = $this->getExpressionsForPlaceholders($column, $this->meta[$migrationMethod]);
            $expressions = array_merge($patternExpressions, $placeholderExpressions);

            if (! empty($expressions)) {
                $this->generateByMeta($expressions);
            }
        }
    }

    /**
     * @param array $column
     * @return array
     */
    final private function getExpressionsByColumn(array $column, array $meta)
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

    /**
     * @param array $column
     * @param array $metaRow
     * @return array
     */
    final private function getExpressionsForPlaceholders(array $column, array $meta)
    {
        if (in_array($column['name'], $this->placeholdersExclude)) {
            return [];
        }

        return array_filter(array_map(function ($m) use ($column) {
            // pattern expressions can not have placeholders
            if (array_key_exists('pattern', $m) || ! array_key_exists('actions', $m)) {
                return;
            }

            // replace placeholders to real column attributes
            $expressions = array_intersect_key(array_flip($m['actions']), $column);
            // sort in order to combine
            asort($expressions);
            asort($column);

            $replaced = array_combine($expressions, $column);
            // restore regular actions (which are not placeholders)
            return array_merge($m['actions'], $replaced);
        }, $meta));
    }

    /**
     * @param array $instructions
     */
    final private function generateByMeta(array $instructions)
    {
        if (is_array(array_values($instructions)[0])) {
            // if array is not flat then generate code recursively
            $this->generateByMeta($instructions[0]);
        } else {
            // call methods from parent to generate code
            foreach ($instructions as $method => $arg) {
                $this->$method($arg);
            }

            $this->endStatement();
        }

    }

}