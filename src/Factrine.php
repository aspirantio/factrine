<?php

namespace Aspirant\Factrine;

use Aspirant\Factrine\EntityBuilder;
use Doctrine\ORM\EntityManager;
use Faker\Factory as FakerFactory;

class Factrine
{
    private   $times = 1;
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var FactoryRegistry
     */
    private $registry;
    /**
     * @var EntityBuilder
     */
    protected $entityBuilder;
    /**
     * @var PersistenceHelper
     */
    protected $persistenceHelper;

    public function __construct(EntityManager $em, FactoryRegistry $registry)
    {
        $this->em = $em;
        $this->registry = $registry;
        $this->entityBuilder = new EntityBuilder($em);
        $this->persistenceHelper = new PersistenceHelper($em);
    }

    /**
     * @param               $entity
     * @param array         $params
     * @param \Closure|null $callback
     *
     * @return array|mixed
     */
    public function create($entity, array $params = [], \Closure $callback = null)
    {
        $result = $this->make($entity, $params, $callback);

        if (is_array($result)) {
            foreach ($result as $entity) {
                $this->persistenceHelper->persist($entity);
            }
        } else {
            $this->persistenceHelper->persist($result);
        }

        return $result;
    }

    /**
     * @param               $factory
     * @param array         $params
     * @param \Closure|null $callback
     *
     * @return array|mixed
     */
    public function make($factory, array $params = [], \Closure $callback = null)
    {
        $result = [];
        $isOneTime = $this->times === 1;

        $dataSet = $this->values($factory, $params);

        if ($isOneTime) {
            $dataSet = [$dataSet];
        }

        foreach ($dataSet as $data) {
            $result[] = $this->entityBuilder->createEntity($factory, $data, $callback);
        }

        return count($result) > 1 ? $result : array_pop($result);
    }

    /**
     * @param string $factory
     * @param array  $params
     *
     * @return array
     */
    public function values($factory, array $params = [])
    {
        $faker = FakerFactory::create();

        for ($i = 1; $i <= $this->times; $i++) {
            $result[] = array_merge(call_user_func_array(
                $this->registry->get($factory)['callable'],
                [$faker]
            ), $params);
        }

        $this->times = 1;

        return count($result) > 1 ? $result : array_pop($result);
    }

    /**
     * @param $times
     *
     * @return $this
     */
    public function times($times)
    {
        $this->times = $times;

        return $this;
    }
}
