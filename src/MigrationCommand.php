<?php

namespace Jeloo\LaraMigrations;

use Illuminate\Config\Repository;
use Illuminate\Console\Command;
use Illuminate\Container\Container;
use Illuminate\Contracts\Filesystem\Filesystem;
use Jeloo\LaraMigrations\MigrationNameParser;

class MigrationCommand extends Command
{

    const COLUMN_DEFINITION_DELIMITER = ',';
    const COLUMN_DEFINITION_ATTRIBUTE_DELIMITER = ':';

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:migration@';

    /**
     * @var string
     */
    protected $signature = 'make:migration@ {name} {columns?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new migration with given console args';

    /**
     * The filesystem instance.
     *
     * @var Filesystem
     */
    protected $files;

    /**
     * The action identifier that determines what code to generate for migration
     *
     * @var string
     */
    protected $verb;

    /**
     * The table name for code generation
     *
     * @var string
     */
    protected $table;

    /**
     * Meta that determines the list of functions that will be called on generated code.
     * The keys are the verbs and the values are the list of explanations how to generate the code.
     *
     * @var Repository
     */
    protected $meta;

    /**
     * The parser of migration name which allows to get verb and a table name from single input string
     *
     * @var MigrationNameParser
     */
    protected $nameParser;

    /**
     * @param Container $app
     */
    public function fire(Container $app)
    {
        /** @var MigrationNameParser $nameParser */
        $this->nameParser = $app->make('make:migration@.nameParser');
        /** @var Repository $meta */
        $this->meta = $app->make('make:migration@.meta');

        $this->table = $this->nameParser->parseTableName();
        $this->verb  = $this->nameParser->parseVerb();

        $this->checkInput();
        $this->generate();
    }

    /**
     * Get the given migration name
     *
     * @return string
     */
    public function getMigrationName()
    {
        return $this->argument('name');
    }

    /**
     * @return array|string
     */
    public function getColumns()
    {
        return $this->argument('columns');
    }

    /**
     * The action name of migration
     * @return string
     */
    public function getMigrationVerb()
    {
        return $this->verb;
    }

    /**
     * Returns array of definitions for schema parsing
     * Column definition array per row
     *
     * @return array
     */
    public function prepareSchema()
    {
        return array_map(function ($columnDefinition) {
            return explode(self::COLUMN_DEFINITION_ATTRIBUTE_DELIMITER, $columnDefinition);
        }, explode(self::COLUMN_DEFINITION_DELIMITER, $this->getColumns()));
    }

    protected function generate()
    {
        //@todo implement MetaBasedGenerator delegation
    }

    /**
     * Validates input
     * @throws \InvalidArgumentException
     */
    protected function checkInput()
    {
        if (! $this->meta->has($this->verb)) {
            throw new \InvalidArgumentException(sprintf(
                'Ensure that meta file has all of the listed keys: %s',
                implode(' or ', $this->nameParser->listAllowedVerbs())
            ));
        }
    }

}