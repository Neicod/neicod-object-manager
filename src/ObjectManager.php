<?php

namespace Neicod\ObjectManager;

use Neicod\ObjectManager\AbstractFactory\AbstractFactoryInterface;
use Neicod\ObjectManager\Factory\FactoryInterface;
use Zend\ConfigAggregator\ConfigAggregator;

use Neicod\ObjectManager\Config\Parameters;
use Neicod\ObjectManager\Config\ParametersInterface;

class ObjectManager implements ObjectManagerInterface
{
    /**
     * @var ConfigInterface
     */
    private $config;

    /**
     * @var array
     */
    private $services = [];


    private $factories = [];
    /**
     * @var
     */
    private $abstractFactories;

    /**
     * ObjectManager constructor.
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        $this->config = new Config($options);
    }

    /**
     * @param array $options
     */
    public function configure(array $options): void
    {
        $this->config->configure($options);
    }

    /**
     * @inheritDoc
     */
    public function get(string $name, array $parameters = [])
    {
        $name = $this->config->resolveName($name);
        if (isset($this->services[$name])) {
            return $this->services[$name];
        }
        $object = $this->createObject($name, $parameters);
        if ($this->config->isShared($name)) {
            $this->services[$name] = $object;
        }
        return $object;
    }

    /**
     * @param string $name
     * @param array $parameters
     * @return mixed
     * @throws \Exception
     */
    private function createObject(string $name, array $parameters = [])
    {
        $factory = $this->getFactory($name);
        return $factory->create($this, $name, $parameters);
    }

    /**
     * @param string $name
     * @return FactoryInterface
     * @throws \Exception
     */
    private function getFactory(string $name): FactoryInterface
    {
        foreach ($this->getAbstractFactories() as $abstractFactory) {
            if ($abstractFactory->canCreate($name)) {
                return $abstractFactory;
            }
        }
        /**
         * @todo doroic wyjatek
         */
        throw new \Exception(sprintf('cant find factory create object %s', $name));
    }

    /**
     * @return AbstractFactoryInterface[]
     */
    private function getAbstractFactories()
    {
        if ($this->abstractFactories === null) {
            $this->abstractFactories = [
                new AbstractFactory\ReflectionAbstractFactory()
            ];
        }
        return $this->abstractFactories;
    }

    /**
     * @inheritDoc
     */
    public function has(string $name): bool
    {
        $name = $this->config->resolveName($name);
        foreach ($this->getAbstractFactories() as $abstractFactory) {
            if ($abstractFactory->canCreate($name)) {
                return true;
            }
        }
        return false;
    }

    /**
     * @inheritDoc
     */
    public function create(string $name, array $parameters = [])
    {
        $name = $this->config->resolveName($name);
        return $this->createObject($name, $parameters);
    }

    /**
     * @param string $name
     * @param array $parameters
     * @return ParametersInterface
     */
    public function getParameters(string $name, array $parameters = []): ParametersInterface
    {
        $data = $this->config->get($name);
        $config = new ConfigAggregator([
            function () use ($data) {
                return $data['parameters'] ?? [];
            },
            function () use ($parameters) {
                return $parameters;
            }
        ]);
        return new Parameters($config->getMergedConfig());
    }
}
