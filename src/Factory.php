<?php

namespace Aspirant\Factrine;

use Doctrine\ORM\EntityManager;

class Factory
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var FactoryRegistry
     */
    private $registry;

    public function __construct(EntityManager $em, FactoryRegistry $registry)
    {
        $this->em = $em;
        $this->registry = $registry;
    }

    /**
     * @return Factrine
     */
    public function create()
    {
        return new Factrine($this->em, $this->registry);
    }

    /**
     * @param string   $identifier
     * @param string   $entity
     * @param callable $callable
     */
    public function addFactory($identifier, $entity, callable $callable)
    {
        $this->registry->add($identifier, [
            'callable' => $callable,
            'entity'   => $entity,
        ]);
    }

    /**
     * @param string   $entity
     * @param callable $callable
     */
    public function addFactoryFor($entity, callable $callable)
    {
        $this->addFactory($entity, $entity, $callable);
    }
}