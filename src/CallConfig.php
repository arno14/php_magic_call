<?php

namespace Arno14\MagicCall;

use Exception;
use Reflection;
use ReflectionClass;

class CallConfig
{
    public array $property_read=[];
    public array $property_write=[];

    public array $debug_logs=[];

    public ReflectionClass $reflection;

    public function __construct(public readonly string $className)
    {
        $this->reflection = new ReflectionClass($className);
    }

    public function readProperty(object $object, string $propertyName): mixed
    {
        if (!isset($this->property_read[$propertyName])) {
            throw new Exception(sprintf('unsupported read property [%s], valid properties are %s', $propertyName, json_encode($this->property_read, JSON_PRETTY_PRINT)));
        }

        $method = $this->property_read[$propertyName];

        if (true!==$method) {
            return call_user_func([$object, $method]);
        }

        $prop = $this->reflection->getProperty($propertyName);

        return $prop->getValue($object);
    }

    public function writeProperty(object $object, string $propertyName, mixed $value): object
    {
        if (!isset($this->property_write[$propertyName])) {
            throw new Exception(sprintf('unsupported write property [%s], valid properties are %s, debug_logs:%s', $propertyName, json_encode($this->property_write, JSON_PRETTY_PRINT), json_encode($this->debug_logs, JSON_PRETTY_PRINT)));
        }

        $method = $this->property_write[$propertyName];

        if (true!==$method) {
            call_user_func([$object, $method], $value);

            return $this;
        }

        $prop = $this->reflection->getProperty($propertyName);

        $prop->setAccessible(true);

        $prop->setValue($object, $value);

        return $object;
    }
}
