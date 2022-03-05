<?php

namespace AliReaza\DependencyInjection;

use AliReaza\Container\NotFoundException;
use Closure;
use ReflectionClass;
use ReflectionException;
use ReflectionFunction;
use ReflectionIntersectionType;
use ReflectionMethod;
use ReflectionNamedType;
use ReflectionParameter;
use ReflectionUnionType;

class DependencyInjectionContainer extends Container
{
    public function __construct(private bool $use_autowiring = false, private array $resolved = [])
    {
    }

    public function resolve(string $id, array $parameters = []): mixed
    {
        if (array_key_exists($id, $this->resolved)) {
            return $this->resolved[$id];
        }

        return $this->resolved[$id] = $this->make($id, $parameters);
    }

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

    public function call(mixed $entry, array $parameters = []): mixed
    {
        if ($entry instanceof Closure) {
            return $this->callFunction($entry, $parameters);
        }

        try {
            return $this->newInstanceEntry($entry, $parameters);
        } catch (ReflectionException) {
            return $entry;
        }
    }

    private function callFunction(Closure $entry, array $parameters = []): mixed
    {
        $reflector = new ReflectionFunction($entry);

        $dependencies = $this->dependencies($reflector, $parameters);

        return $reflector->invokeArgs($dependencies);
    }

    private function dependencies(ReflectionFunction|ReflectionMethod $reflector, array $parameters = []): array
    {
        $dependencies = [];

        $reflection_parameters = $reflector->getParameters();
        foreach ($reflection_parameters as $reflection_parameter) {
            $parameter_name = $reflection_parameter->getName();
            $parameter_name_with_dollar = '$' . $parameter_name;

            if (array_key_exists($parameter_name_with_dollar, $parameters)) {
                $dependencies[] = $parameters[$parameter_name_with_dollar];
            } else if (array_key_exists($parameter_name, $parameters)) {
                $dependencies[] = $parameters[$parameter_name];
            } else if (array_key_exists($reflection_parameter->getPosition(), $parameters)) {
                $dependencies[] = $parameters[$reflection_parameter->getPosition()];
            } else if ($reflection_parameter->isDefaultValueAvailable()) {
                $dependencies[] = $reflection_parameter->getDefaultValue();
            } else {
                $parameter_type = $reflection_parameter->getType();

                $dependencies[] = is_null($parameter_type)
                    ? $this->resolve($parameter_name_with_dollar)
                    : $this->resolveParameterWithType($reflection_parameter);
            }
        }

        return $dependencies;
    }

    private function resolveParameterWithType(ReflectionParameter $reflection_parameter): mixed
    {
        $types = $this->getParameterAllTypes($reflection_parameter);

        $exception = null;

        foreach ($types as $type) {
            $name = $type instanceof ReflectionNamedType ? $type->getName() : (string)$type;

            try {
                return $this->resolve($name);
            } catch (NotFoundException $exception) {
                continue;
            }
        }

        throw $exception;
    }

    private function getParameterAllTypes(ReflectionParameter $reflection_parameter): array
    {
        $reflection_type = $reflection_parameter->getType();

        if (is_null($reflection_type)) {
            return [];
        }

        if ($reflection_type instanceof ReflectionUnionType || $reflection_type instanceof ReflectionIntersectionType) {
            return $reflection_type->getTypes();
        }

        return [$reflection_type];
    }

    private function newInstanceEntry(mixed $entry, array $parameters = []): mixed
    {
        if ($this->isValidCallableEntry($entry)) {
            return $this->callClassWithMethod($entry, $parameters);
        }

        return $this->callClass($entry, $parameters);
    }

    private function isValidCallableEntry(mixed $entry): bool
    {
        return is_array($entry) && !empty($entry) && !empty($entry[0]) && (is_string($entry[0]) || is_array($entry[0]));
    }

    private function callClassWithMethod(array $entry, array $parameters = []): mixed
    {
        $method = $entry[1] ?? '__construct';
        $_entry = $entry[0];

        $class_parameters = [];
        if (is_array($_entry)) {
            $class_parameters = $_entry[1] ?? [];
            $_entry = $_entry[0];
        }

        $object = $this->callClass($_entry, $class_parameters);
        return $this->callMethod($object, $method, $parameters);
    }

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

    private function callMethod(object $object, string $method, array $parameters = []): mixed
    {
        $reflector = new ReflectionClass($object);

        $method = $reflector->getMethod($method);
        $dependencies = $this->dependencies($method, $parameters);

        return $method->invokeArgs($object, $dependencies);
    }

    public function useAutowiring(bool $use_autowiring = true): void
    {
        $this->use_autowiring = $use_autowiring;
    }
}
