<?php

namespace Jeloo\LaraMigrations;

/**
 * Parse migration name in order to get verb, table name and some migration schema details
 * Class MigrationNameParser
 * @package Jeloo\LaraMigrations
 */
class MigrationNameParser
{
    /**
     * @var string
     */
    private $migrationName;

    /**
     * @var array
     */
    private $allowedVerbs = [
        'create',
        'add',
        'drop',
    ];

    /**
     * @var array
     */
    private $tableNameDelimiters = ['__', '_table'];

    const DELIMITER = '_';

    public function __construct($migrationName)
    {
        $this->migrationName = $migrationName;
    }

    /**
     * @return string
     * @throws \InvalidArgumentException
     */
    public function parseTableName()
    {
        if (! array_key_exists(1, explode(self::DELIMITER, $this->migrationName))) {
            throw new \InvalidArgumentException('Migration name is invalid: second word must represent table name');
        }

        $start = strpos($this->migrationName, self::DELIMITER) + 1;
        $end = $this->getLastTableNamePos() - $start;

        return substr(
            $this->migrationName,
            $start,
            $end
        );
    }

    /**
     * @return string
     * @throws \InvalidArgumentException
     */
    public function parseVerb()
    {
        $verb = substr($this->migrationName, 0, strpos($this->migrationName, self::DELIMITER));

        if (! in_array($verb, $this->allowedVerbs)) {
            throw new \InvalidArgumentException(
                sprintf('Invalid verb: %s. Allowed verbs: %s', $verb, implode(', ', $this->allowedVerbs))
            );
        }

        return $verb;
    }

    /**
     * Returns the list of allowed verbs prefixes
     *
     * @return array
     */
    public function listAllowedVerbs()
    {
        return $this->allowedVerbs;
    }

    /**
     * Get string right position of the table name
     *
     * @return int
     */
    final private function getLastTableNamePos()
    {
        foreach ($this->tableNameDelimiters as $delimiter) {
            $end = strpos($this->migrationName, $delimiter);
            if ($end !== false) {
                return $end;
            }
        }

        return strlen($this->migrationName);
    }

}