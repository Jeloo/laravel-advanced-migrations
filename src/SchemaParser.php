<?php

namespace Jeloo\LaraMigrations;

class SchemaParser
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
        'integer' => ['id', '+_id'],
        'string'  => ['email', 'name', 'title'],
        'text'    => ['description', 'path']
    ];

    /**
     * @param array $input
     */
    public function __construct(array $input)
    {
        $this->input = $input;
    }

    /**
     * @return array
     */
    public function parse()
    {
        return array_map(function ($colSettings) {
            $type = $this->parseType($colSettings);

            return [
                'name'       => $this->getName($colSettings),
                'type'       => $type,
                'properties' => $this->parseProperties($colSettings, $type)
            ];
        }, $this->input);
    }

    protected function getName($colSettings)
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
            throw new \Exception('Type is not specified and can not be identified automatically or invalid');
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
            var_dump($regex, $name);
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
        $typeIndex = array_search($type, $colSettings);

        unset($colSettings[0], $colSettings[$typeIndex]);

        return array_values($colSettings);
    }

}