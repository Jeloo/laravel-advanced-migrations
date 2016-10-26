<?php

namespace Jeloo\LaraMigrations;

use Illuminate\Contracts\Config\Repository as Meta;
use Illuminate\Support\Str;

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
        return $this->output;
    }

    /**
     * @param string $migrationMethod
     */
    final private function handleTable($migrationMethod)
    {
        foreach ($this->meta[$migrationMethod] as $m) {
            if (! array_key_exists('expressions', $m)) {
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
            $expressions = $this->getExpressionsByColumn($column, $this->meta[$migrationMethod]);

            if (! empty($expressions)) {
                foreach ($expressions as $exp) {
                    $this->generateByMeta($exp);
                }
            }
        }
    }

    /**
     * @param array $column
     * @return array
     */
    final private function getExpressionsByColumn(array $column, array $meta)
    {
        $expressions = [];

        foreach ($meta as $m) {
            if (! array_key_exists('expressions', $m)) continue;

            if (! array_key_exists('pattern', $m) || ! empty(array_intersect($column, $m['pattern']))) {
                // expression is suitable for all columns
                $this->addExpressions($expressions, $m, $column);
                continue;
            }

            if (array_key_exists('name', $m['pattern']) && @preg_match($m['pattern']['name'], $column['name'])) {
                $this->addExpressions($expressions, $m, $column);
            }
        }

        return $expressions;
    }

    final private function addExpressions(array &$expressions, array $meta, array $column)
    {
        if (isset($meta['expressions'][0]) && is_array($meta['expressions'][0])) {
            $nestedExpressions = array_map(function ($nestedExpressions) use ($column) {
                return $this->replacePlaceholders($nestedExpressions, $column);
            }, $meta['expressions']);
            $expressions = array_merge($expressions, $nestedExpressions);
        } else {
            array_push($expressions, $this->replacePlaceholders($meta['expressions'], $column));
        }
    }

    /**
     * @param array $column
     * @param array $metaRow
     * @return array
     */
    final private function replacePlaceholders(array $expressions, array $column)
    {
        //dd($expressions, $column);

        $replaced = [];

        foreach ($expressions as $exp => $subject) {
            if (Str::startsWith($subject, '{') && Str::endsWith($subject, '}')) {
                $withoutBraces = preg_replace('/[{}]/', '', $subject);
                $replaced[$exp] = array_key_exists($withoutBraces, $column) ? $column[$withoutBraces] : $subject;
            } else {
                $replaced[$exp] = $subject;
            }
        }

        return $replaced;
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
                $method = $arg === 'end' ? 'endStatement' : $method;
                $this->$method($arg);
            }

        }

    }

}