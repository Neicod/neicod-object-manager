<?php

namespace Neicod\ObjectManager;

interface ObjectManagerInterface
{
    /**
     * @param string $name
     * @param array $parameters
     * @return mixed
     */
    public function get(string $name, array $parameters = []);

    /**
     * @param string $name
     * @return bool
     */
    public function has(string $name): bool;

    /**
     * @param string $name
     * @param array $parameters
     * @return mixed
     */
    public function create(string $name, array $parameters = []);
}
