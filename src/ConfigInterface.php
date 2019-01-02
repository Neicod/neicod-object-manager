<?php

namespace Neicod\ObjectManager;

interface ConfigInterface
{
    /**
     * @param array $options
     */
    public function configure(array $options): void;

    /**
     * @param string $name
     * @return array
     */
    public function get(string $name): array;
}
