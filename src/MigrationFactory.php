<?php

namespace Jeloo\LaraMigrations;

class MigrationFactory
{

    private $classMap = [
        'create' => TableCreationGeneratorTest::class
    ];

    /**
     * @param string $migrationMethod
     * @param string $verb
     * @param array $fields
     * @return mixed
     */
    public function make($verb = 'create', array $fields)
    {
        if (! array_key_exists($verb, $this->classMap)) {
            throw new \InvalidArgumentException(sprintf('Invalid migration type: %s', $verb));
        }

        return new $this->classMap[$verb]($fields);
    }

}