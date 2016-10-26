<?php

namespace Jeloo\LaraMigrations;

abstract class AbstractGenerator
{

    protected $output = '';

    abstract public function generateUp();

    abstract public function generateDown();

    protected function call($methodName)
    {
        $this->fillDefaults();
        $this->output .= sprintf('{object}->%s({args})', $methodName);
        return $this;
    }

    protected function of($variableName)
    {
        $pattern = '/\{object\}/';
        $this->output = preg_replace($pattern, $variableName, $this->output);
        return $this;
    }

    /**
     * @param array|string $arguments
     * @return $this
     */
    protected function withArgs($arguments)
    {
        $arguments = is_array($arguments) ? $arguments : [$arguments];

        $pattern = '/\{args\}/';

        $arguments = array_map(function ($arg) {
            return '\''.$arg.'\'';
        }, $arguments);

        $this->output = preg_replace($pattern, implode(', ', $arguments), $this->output);
        return $this;
    }

    protected function callChain($methodName)
    {
        $this->fillDefaults();
        $this->output .= sprintf("->%s({args})", $methodName);
        return $this;
    }

    final protected function endStatement()
    {
        $this->fillDefaults();

        if ($this->output) {
            $this->output .= ';'.PHP_EOL;
        }
    }

    final private function fillDefaults()
    {
        $this->withArgs([]);
    }

}