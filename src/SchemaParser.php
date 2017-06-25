<?php

namespace Jeloo\LaraMigrations;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;

class SchemaParser implements SchemaParserInterface
{

    /**
     * @var array
     */
    private $input;

    /**
     * @var array
     */
    private $availableTypes = [
        'integer', 'string'
    ];

    /**
     * @var array
     */
    private $possibleColTypes = [
        'integer' => ['id', '.+_id'],
        'string'  => ['email', 'name', 'title'],
        'text'    => ['description', 'path']
    ];

    public function __construct(array $input)
    {
        $this->input = $input;
    }

    /**
     * @{inheritdoc}
     */
    public function parse()
    {
        return array_map(function ($colSettings) {
            $type = $this->parseType($colSettings);

            $schema = [
                'name'       => $this->getName($colSettings),
                'type'       => $type,
                'properties' => $this->parseProperties($colSettings, $type)
            ];

            if (
                array_search('foreign', $schema['properties']) !== false &&
                $relatedTable = $this->guessRelatedTableByName($colSettings)
            ) {
                $schema['belongsTo'] = $relatedTable;
            }

            return $schema;

        }, $this->input);
    }

    /**
     * @param array $colSettings
     * @return string
     */
    protected function getName(array $colSettings)
    {
        return $colSettings[0];
    }

    /**
     * @param array $colSettings
     * @return string - the type
     * @throws \Exception
     */
    protected function parseType(array $colSettings)
    {
        if ($intersect = array_intersect($this->availableTypes, $colSettings)) {
            return array_shift($intersect);
        }

        if (! $type = $this->guessTypeByName($this->getName($colSettings))) {
            $type = 'string';
        }

        return $type;
    }

    /**
     * @param $name
     * @return bool|string
     */
    protected function guessTypeByName($name)
    {
        foreach ($this->possibleColTypes as $type => $names) {
            $regex = sprintf('/^(%s)/', implode('|', $this->possibleColTypes[$type]));
            if (preg_match($regex, $name)) {
                return $type;
            }
        }

        return false;
    }

    /**
     * @param array $colSettings
     * @param string $type
     * @return array
     */
    protected function parseProperties(array $colSettings, $type)
    {
        //exclude name column
        $result = array_except($colSettings, 0);
        $typeIndex = array_search($type, $colSettings);

        $result = $typeIndex ? array_except($result, $typeIndex) : $result;
        // add foreign attribute if column looks like foreign column
        if (Str::endsWith($this->getName($colSettings), '_id')) {
           $result = array_merge($result, ['foreign', 'unsigned', 'nullable']);
        }

        return array_values($result);
    }

    /**
     * @param array $colSettings
     * @return bool|string
     */
    protected function guessRelatedTableByName(array $colSettings)
    {
        if (Str::endsWith($this->getName($colSettings), '_id')) {
            $table = str_replace('_id', '', $this->getName($colSettings));
            return Schema::hasTable($table) ? $table : Str::plural($table);
        }

        return false;
    }

}