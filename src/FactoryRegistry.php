<?php

namespace Aspirant\Factrine;

class FactoryRegistry
{
    /**
     * Will hold all factories
     *
     * @var array
     */
    protected $factories = [];

    /**
     * @return array
     */
    public function all()
    {
        return $this->factories;
    }

    /**
     * @param string $identifier
     * @param array  $factoryDefinition
     */
    public function add($identifier, array $factoryDefinition)
    {
        $this->factories[$identifier] = $factoryDefinition;
    }

    /**
     * @param $identifier
     *
     * @return callable
     */
    public function get($identifier)
    {
        return $this->factories[$identifier];
    }
}
