<?php

namespace Neicod\ObjectManager\Config;

class Parameters implements ParametersInterface
{

    /**
     * @var array
     */
    private $data = [];

    /**
     * Parameter constructor.
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * @inheritDoc
     */
    public function has(string $name): bool
    {
        return (isset($this->data[$name]));
    }

    /**
     * @inheritDoc
     */
    public function get(string $name)
    {
        return $this->data[$name]?? null;
    }
}
