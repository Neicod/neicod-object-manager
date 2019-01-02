<?php

namespace Neicod\ObjectManager\AbstractFactory;

use ReflectionClass;
use ReflectionParameter;
use Neicod\ObjectManager\ObjectManagerInterface;

class ReflectionAbstractFactory implements AbstractFactoryInterface
{
    /**
     * @inheritDoc
     */
    public function canCreate(string $name): bool
    {
        return class_exists($name) && $this->canCallConstructor($name);
    }

    /**
     * @param string $name
     * @return bool
     * @throws \ReflectionException
     */
    private function canCallConstructor(string $name): bool
    {
        $constructor = (new ReflectionClass($name))->getConstructor();

        return $constructor === null || $constructor->isPublic();
    }

    /**
     * @inheritDoc
     */
    public function create(ObjectManagerInterface $objectManager, string $name, array $parameters = [])
    {
        $reflectionClass = new ReflectionClass($name);
        if (null === ($constructor = $reflectionClass->getConstructor())) {
            return new $name();
        }
        $reflectionParameters = $constructor->getParameters();
        if (empty($reflectionParameters)) {
            return new $name();
        }
        $resolver = function (ReflectionParameter $parameter) use ($objectManager, $name) {
            return $this->resolveParameter($parameter, $objectManager, $name);
        };
        $parameters = array_map($resolver, $reflectionParameters);
        return new $name(...$parameters);
    }

    /**
     * @param ReflectionParameter $parameter
     * @param ObjectManagerInterface $objectManager
     * @param string $name
     * @return array|mixed
     * @throws \Exception
     */
    private function resolveParameter(ReflectionParameter $parameter, ObjectManagerInterface $objectManager, string $name)
    {
        if ($parameter->isArray()) {
            return [];
        }
        $parameters = $objectManager->getParameters($name);
        if (!$parameter->getClass()) {
            if ($parameters->has($parameter->getName())){
                return $parameters->get($parameter->getName());
            }
            if (!$parameter->isDefaultValueAvailable()) {
                throw new \Exception(sprintf(
                    'Unable to create service "%s"; unable to resolve parameter "%s" '
                    . 'to a class, interface, or array type',
                    $name,
                    $parameter->getName()
                ));
            }
            return $parameter->getDefaultValue();
        }
        if ($parameters->has($parameter->getName())){
            $type = $parameters->get($parameter->getName());
        } else {
            $type = $parameter->getClass()->getName();
        }
        if ($objectManager->has($type)) {
            return $objectManager->get($type);
        }

        if (!$parameter->isOptional()) {
            throw new \Exception(sprintf(
                'Unable to create service "%s"; unable to resolve parameter "%s" using type hint "%s"',
                $name,
                $parameter->getName(),
                $type
            ));
        }
        // Type not available in container, but the value is optional and has a
        // default defined.
        return $parameter->getDefaultValue();
    }

}
