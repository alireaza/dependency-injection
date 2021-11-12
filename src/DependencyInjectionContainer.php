<?php

namespace AliReaza\DependencyInjection;

use Closure;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use ReflectionClass;
use ReflectionException;
use ReflectionFunction;
use ReflectionMethod;

/**
 * Class DependencyInjectionContainer
 *
 * @package AliReaza\DependencyInjection
 */
class DependencyInjectionContainer extends Container
{
    /**
     * @param bool $use_autowiring
     * @param array $resolved
     */
    public function __construct(private bool $use_autowiring = false, private array $resolved = [])
    {
    }

    /**
     * @param string $id
     * @param array $parameters
     *
     * @return mixed
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws ReflectionException
     */
    public function resolve(string $id, array $parameters = []): mixed
    {
        if (array_key_exists($id, $this->resolved)) {
            return $this->resolved[$id];
        }

        return $this->resolved[$id] = $this->make($id, $parameters);
    }

    /**
     * @param string $id
     * @param array $parameters
     *
     * @return mixed
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws ReflectionException
     */
    public function make(string $id, array $parameters = []): mixed
    {
        if ($id === static::class) {
            return $this;
        }

        if ($this->use_autowiring && !$this->has($id)) {
            $this->set($entry = $id, $entry);
        } else {
            $entry = parent::get($id);
        }

        return $this->call($entry, $parameters);
    }

    /**
     * @param mixed $entry
     * @param array $parameters
     *
     * @return mixed
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws ReflectionException
     */
    public function call(mixed $entry, array $parameters = []): mixed
    {
        if ($entry instanceof Closure) {
            return $this->callFunction($entry, $parameters);
        }

        try {
            if (is_array($entry)) {
                $method = $entry[1] ?? '__construct';
                $entry = $entry[0];

                $classParameters = [];
                if (is_array($entry)) {
                    $classParameters = $entry[1] ?? [];
                    $entry = $entry[0];
                }

                $object = $this->callClass($entry, $classParameters);

                return $this->callMethod($object, $method, $parameters);
            }

            return $this->callClass($entry, $parameters);
        } catch (ReflectionException) {
            return $entry;
        }
    }

    /**
     * @param Closure $entry
     * @param array $parameters
     *
     * @return mixed
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws ReflectionException
     */
    private function callFunction(Closure $entry, array $parameters = []): mixed
    {
        $reflector = new ReflectionFunction($entry);

        $dependencies = $this->dependencies($reflector, $parameters);

        return $reflector->invokeArgs($dependencies);
    }

    /**
     * @param string|object $entry
     * @param array $parameters
     *
     * @return string|object
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws ReflectionException
     */
    private function callClass(string|object $entry, array $parameters = []): string|object
    {
        $reflector = new ReflectionClass($entry);

        if ($reflector->isInstantiable()) {
            $constructor = $reflector->getConstructor();

            if (is_null($constructor) || ($constructor->getNumberOfRequiredParameters() === 0 && empty($parameters))) {
                $entry = $reflector->newInstance();
            } else {
                $dependencies = $this->dependencies($constructor, $parameters);

                $entry = $reflector->newInstanceArgs($dependencies);
            }
        }

        return $entry;
    }

    /**
     * @param object $object
     * @param string $method
     * @param array $parameters
     *
     * @return mixed
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws ReflectionException
     */
    private function callMethod(object $object, string $method, array $parameters = []): mixed
    {
        $reflector = new ReflectionClass($object);

        $method = $reflector->getMethod($method);
        $dependencies = $this->dependencies($method, $parameters);

        return $method->invokeArgs($object, $dependencies);
    }

    /**
     * @param ReflectionFunction|ReflectionMethod $reflector
     * @param array $parameters
     *
     * @return array
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws ReflectionException
     */
    private function dependencies(ReflectionFunction|ReflectionMethod $reflector, array $parameters = []): array
    {
        $dependencies = [];

        $reflectionParameters = $reflector->getParameters();
        foreach ($reflectionParameters as $reflectionParameter) {
            $parameter_name = '$' . $reflectionParameter->getName();

            if (array_key_exists($parameter_name, $parameters)) {
                $dependencies[] = $parameters[$parameter_name];
            } else if (array_key_exists($reflectionParameter->getName(), $parameters)) {
                $dependencies[] = $parameters[$reflectionParameter->getName()];
            } else if (array_key_exists($reflectionParameter->getPosition(), $parameters)) {
                $dependencies[] = $parameters[$reflectionParameter->getPosition()];
            } else if ($reflectionParameter->isDefaultValueAvailable()) {
                $dependencies[] = $reflectionParameter->getDefaultValue();
            } else {
                $parameter_type = $reflectionParameter->getType();

                if (is_null($parameter_type)) {
                    $id = $parameter_name;
                } else {
                    $id = $parameter_type->getName();
                }

                $dependencies[] = $this->resolve($id);
            }
        }

        return $dependencies;
    }

    /**
     * @param bool $bool
     */
    public function useAutowiring(bool $bool = true): void
    {
        $this->use_autowiring = $bool;
    }
}
