<?php

namespace Neicod\ObjectManager\Factory;

use Neicod\ObjectManager\ObjectManagerInterface;

interface FactoryInterface
{

    /**
     * @param ObjectManagerInterface $objectManager
     * @param string $name
     * @param array $parameters
     * @return mixed
     */
    public function create(ObjectManagerInterface $objectManager, string $name, array $parameters = []);
}
