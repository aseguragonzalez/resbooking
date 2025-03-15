<?php

declare(strict_types=1);

namespace Seedwork\Infrastructure\Mvc\Requests;

use Seedwork\Infrastructure\Mvc\Requests\{InvalidDocComment, InvalidObjectType, InvalidRequestType};

final class MvcRequestBuilder implements RequestBuilder
{
    /** @var array<string, string|int|float> */
    private array $args = [];

    /** @var class-string */
    private string $requestType;

    public function __construct()
    {
    }

    /**
     * @param class-string $requestType
     */
    public function withRequestType(string $requestType): RequestBuilder
    {
        if (!class_exists($requestType)) {
            throw new InvalidRequestType("Class $requestType does not exist.");
        }

        $this->requestType = $requestType;

        return $this;
    }

    /**
     * @param array<string, string|int|float> $args
     */
    public function withArgs(array $args): RequestBuilder
    {
        $this->args = $args;

        return $this;
    }

    public function build(): object
    {
        $reflectionClass = new \ReflectionClass($this->requestType);
        $constructor = $reflectionClass->getConstructor();
        $constructorParameters = $constructor ? $constructor->getParameters() : [];
        $arguments = array_map(
            function (\ReflectionParameter $param) {
                $args = array_filter(
                    $this->args,
                    fn ($key) => str_starts_with($key, $param->getName() . '.')
                        || str_starts_with($key, $param->getName() . '['),
                    ARRAY_FILTER_USE_KEY
                );
                return (array_key_exists($param->getName(), $this->args) || !empty($args))
                    ? $this->getArgumentValue($param)
                    : $this->getDefaultValue($param);
            },
            $constructorParameters
        );
        return $reflectionClass->newInstanceArgs($arguments);
    }

    private function getArgumentValue(\ReflectionParameter $param): mixed
    {
        $type = $param->getType();
        if (!($type instanceof \ReflectionNamedType)) {
            throw new InvalidObjectType(objectType: (string)$type);
        }

        $name = $param->getName();
        return match ($type->getName()) {
            'int' => (int)$this->args[$name],
            'float' => (float)$this->args[$name],
            'string' => (string)$this->args[$name],
            'bool' => (bool)$this->args[$name],
            \DateTime::class => new \DateTime((string)$this->args[$name]),
            \DateTimeImmutable::class => new \DateTimeImmutable((string)$this->args[$name]),
            'array' => $this->getEmbeddedArray($param, $name),
            default => $type->isBuiltin() ? $this->args[$name] : $this->getEmbeddedObject($param, $name),
        };
    }

    private function getEmbeddedArray(\ReflectionParameter $param, string $path): mixed
    {
        $type = $param->getType();
        if (!$type instanceof \ReflectionNamedType) {
            throw new InvalidObjectType(objectType: (string)$type);
        }

        $args = array_filter($this->args, fn ($key) => str_starts_with($key, $path . '['), ARRAY_FILTER_USE_KEY);
        $itemType = $this->getArrayItemTypeFromDocComment($param);
        $builtInTypes = ['int', 'float', 'string', 'bool', \DateTime::class, \DateTimeImmutable::class];
        if (class_exists($itemType) && !in_array($itemType, $builtInTypes, true)) {
            return $this->getEmbeddedObjectArray($itemType, $args);
        }

        return array_map(function ($value) use ($itemType) {
            return match ($itemType) {
                'int' => (int)$value,
                'float' => (float)$value,
                'string' => (string)$value,
                'bool' => (bool)$value,
                \DateTime::class => new \DateTime((string)$value),
                \DateTimeImmutable::class => new \DateTimeImmutable((string)$value),
                default => $value,
            };
        }, array_values($args));
    }

    private function getArrayItemTypeFromDocComment(\ReflectionParameter $param): string
    {
        $paramName = $param->getName();
        if (!$paramName) {
            throw new InvalidParamName((string)$param);
        }

        $docComment = $param->getDeclaringFunction()->getDocComment();
        if (!$docComment) {
            throw new InvalidDocComment($paramName);
        }

        $pattern = sprintf('/@param\s+array<(\w+)>\s+\$%s/', preg_quote($paramName, '/'));
        if (preg_match($pattern, $docComment, $matches)) {
            $itemType = $matches[1];
            if (in_array($itemType, ['int', 'float', 'string', 'bool'], true)) {
                return $itemType;
            }
            if (strpos($itemType, '\\') === false) {
                $reflectionClass = $param->getDeclaringClass();
                if (is_null($reflectionClass)) {
                    throw new InvalidObjectType($itemType);
                }
                $namespace = $reflectionClass->getNamespaceName();
                $itemType = $namespace . '\\' . $itemType;
            }
            return $itemType;
        }

        throw new InvalidDocComment($paramName);
    }

    /**
     * @param array<string, string|int|float> $args
     */
    private function getEmbeddedObjectArray(string $type, array $args): mixed
    {
        $groupedArgs = array_unique(array_map(fn($key) => strstr($key ? $key : '', '.', true), array_keys($args)));
        $embeddedObjects = array_map(function ($group) use ($type, $args) {
            $filteredArgs = array_filter($args, fn($key) => str_starts_with($key, $group . '.'), ARRAY_FILTER_USE_KEY);
            $embeddedArgs = array_combine(
                array_map(fn($key) => substr($key, strlen($group ? $group : '') + 1), array_keys($filteredArgs)),
                $filteredArgs
            );
            /** @var class-string $typeName */
            $typeName = $type;
            return (new self())->withRequestType($typeName)->withArgs($embeddedArgs)->build();
        }, $groupedArgs);
        return array_values($embeddedObjects);
    }

    private function getEmbeddedObject(\ReflectionParameter $param, string $path): mixed
    {
        $type = $param->getType();
        if (!($type instanceof \ReflectionNamedType)) {
            throw new InvalidObjectType((string)$param);
        }

        $args = array_filter($this->args, fn ($key) => str_starts_with($key, $path . '.'), ARRAY_FILTER_USE_KEY);
        $objectArgs = array_combine(array_map(fn ($key) => substr($key, strlen($path) + 1), array_keys($args)), $args);
        /** @var class-string $typeName */
        $typeName = $type->getName();
        return (new self())->withRequestType($typeName)->withArgs($objectArgs)->build();
    }

    private function getDefaultValue(\ReflectionParameter $param): mixed
    {
        return $param->isDefaultValueAvailable() ? $param->getDefaultValue() : null;
    }
}
