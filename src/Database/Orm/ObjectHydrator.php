<?php

declare(strict_types=1);

namespace Palet\Framework\Database\Orm;

use Palet\Framework\Contracts\Database\Orm\HydratorInterface;
use ReflectionClass;
use ReflectionProperty;

class ObjectHydrator implements HydratorInterface
{
    /** @var array<string, array<string, ReflectionProperty>> */
    protected array $reflectionCache = [];

    public function hydrate(array $data, object $object): object
    {
        $properties = $this->getProperties($object::class);

        foreach ($data as $key => $value) {
            if (isset($properties[$key])) {
                $property = $properties[$key];
                $property->setValue($object, $value);
            }
        }

        return $object;
    }

    public function extract(object $object): array
    {
        $properties = $this->getProperties($object::class);
        $data = [];

        foreach ($properties as $name => $property) {
            if ($property->isInitialized($object)) {
                $data[$name] = $property->getValue($object);
            }
        }

        return $data;
    }

    protected function getProperties(string $class): array
    {
        if (isset($this->reflectionCache[$class])) {
            return $this->reflectionCache[$class];
        }

        $reflection = new ReflectionClass($class);
        $properties = [];

        foreach ($reflection->getProperties() as $property) {
            $property->setAccessible(true);
            $properties[$property->getName()] = $property;
        }

        $this->reflectionCache[$class] = $properties;

        return $properties;
    }
}
