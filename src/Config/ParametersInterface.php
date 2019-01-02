<?php

namespace Neicod\ObjectManager\Config;

interface ParametersInterface
{
    /**
     * @param string $name
     * @return bool
     */
    public function has(string $name): bool;

    /**
     * @param string $name
     * @return mixed
     */
    public function get(string $name);
}
