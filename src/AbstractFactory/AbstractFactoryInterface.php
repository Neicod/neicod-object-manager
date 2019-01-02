<?php

namespace Neicod\ObjectManager\AbstractFactory;

use Neicod\ObjectManager\Factory\FactoryInterface;

interface AbstractFactoryInterface extends FactoryInterface
{

    /**
     * @param string $name
     * @return bool
     */
    public function canCreate(string $name): bool;
}
