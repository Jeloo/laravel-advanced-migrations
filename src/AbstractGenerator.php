<?php

namespace Jeloo\LaraMigrations;

abstract class AbstractGenerator
{

    const INDENT_LINES_NUM = 12;

    protected $output = [
        'up'   => '',
        'down' => ''
    ];

    /**
     * [up] or [down]
     * @var string
     */
    protected $migrationMethod = 'up';

    abstract public function generateUp();

    abstract public function generateDown();

    protected function call($methodName)
    {
        $this->fillDefaults();
        $this->addOutput(sprintf('{object}->%s({args})', $methodName));
        return $this;
    }

    /**
     * @param  string $variableName
     * @return        $this
     */
    protected function of($variableName)
    {
        $pattern = '/\{object\}/';
        $this->output[$this->migrationMethod] = preg_replace($pattern, $variableName, $this->getOutput());
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

        $this->output[$this->migrationMethod] = preg_replace($pattern, implode(', ', $arguments), $this->getOutput());

        return $this;
    }

    /**
     * @param  string $methodName
     * @return $this
     */
    protected function callChain($methodName)
    {
        $this->fillDefaults();
        $this->addOutput(sprintf("->%s({args})", $methodName));
        return $this;
    }

    final protected function endStatement()
    {
        $this->fillDefaults();

        if ($this->getOutput()) {
            $this->addOutput(';'.PHP_EOL.str_repeat(' ', self::INDENT_LINES_NUM));
        }
    }

    /**
     * @param  string $migrationMethod
     * @return $this
     */
    final protected function setMigrationMethod($migrationMethod = 'up')
    {
        $this->migrationMethod = $migrationMethod;
        return $this;
    }

    /**
     * @return string
     */
    final protected function getOutput()
    {
        return $this->output[$this->migrationMethod];
    }

    final private function fillDefaults()
    {
        $this->withArgs([]);
    }

    /**
     * @param  string $output
     * @return        $this
     */
    final private function addOutput($output)
    {
        $this->output[$this->migrationMethod] .= $output;
        return $this;
    }

}