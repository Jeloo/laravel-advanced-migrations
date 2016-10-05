<?php

namespace Jeloo\LaraMigrations;

abstract class AbstractGenerator
{

    protected $output = '';

    abstract public function generateUp();

    abstract public function generateDown();

    public function call($methodName)
    {
        $this->fillDefaults();
        $this->output .= sprintf('%object%->%s(%args%)', $methodName);
        return $this;
    }

    public function of($variableName)
    {
        $pattern = '/^%object%$/';
        $this->output = preg_replace($pattern, $variableName, $this->output);
        return $this;
    }

    public function withArgs(array $arguments)
    {
        $pattern = '/^%args%$/';
        $this->output = preg_replace($pattern, implode(', ', $arguments), $this->output);
        return $this;
    }

    public function callChain($methodName)
    {
        $this->fillDefaults();
        $this->output .= sprintf('->%s(%args%)', $methodName);
        return $this;
    }

    final private function fillDefaults()
    {
        $this->withArgs([]);
    }

    final private function endStatement()
    {
        if ($this->output) {
            $this->output .= ';'.PHP_EOL;
        }
    }

    public function __destruct()
    {
        $this->endStatement();
    }

}