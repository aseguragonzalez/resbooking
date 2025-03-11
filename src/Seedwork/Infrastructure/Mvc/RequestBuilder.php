<?php

declare(strict_types=1);

namespace Seedwork\Infrastructure\Mvc;

use PHPUnit\Event\Runtime\Runtime;
use RuntimeException;
use Tuupola\Ksuid;
use Tuupola\KsuidFactory;

final class RequestBuilder
{
    /** @var array<string, string> */
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
            throw new \InvalidArgumentException("Class $requestType does not exist.");
        }

        $this->requestType = $requestType;

        return $this;
    }

    /**
     * @param array<string, string> $args
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
        if (!$type instanceof \ReflectionNamedType) {
            throw new RuntimeException('Type is union or intersection');
        }
        $name = $param->getName();
        return match ($type->getName()) {
            'int' => (int)$this->args[$name],
            'float' => (float)$this->args[$name],
            'string' => (string)$this->args[$name],
            'bool' => (bool)$this->args[$name],
            \DateTime::class => new \DateTime((string)$this->args[$name]),
            \DateTimeImmutable::class => new \DateTimeImmutable((string)$this->args[$name]),
            Ksuid::class => KsuidFactory::fromString((string)$this->args[$name]),
            'array' => $this->getEmbeddedArray($param, $name),
            default => $type->isBuiltin() ? $this->args[$name] : $this->getEmbeddedObject($param, $name),
        };
    }

    private function getArrayItemTypeFromDocComment(\ReflectionParameter $param): string
    {
        $paramName = $param->getName();
        if (!$paramName) {
            throw new RuntimeException('Parameter name not found');
        }

        $docComment = $param->getDeclaringFunction()->getDocComment();
        if (!$docComment) {
            throw new RuntimeException("Doc comment not found for parameter $paramName");
        }

        $pattern = sprintf('/@param\s+array<(\w+)>\s+\$%s/', preg_quote($paramName, '/'));
        if (preg_match($pattern, $docComment, $matches)) {
            return $matches[1];
        }

        throw new RuntimeException("Array item type not found in doc comment for parameter $paramName");
    }

    private function getEmbeddedArray(\ReflectionParameter $param, string $path): mixed
    {
        $type = $param->getType();
        if (!$type instanceof \ReflectionNamedType) {
            throw new RuntimeException('Type is union or intersection');
        }
        $args = array_filter($this->args, fn ($key) => str_starts_with($key, $path . '['), ARRAY_FILTER_USE_KEY);
        $itemType = $this->getArrayItemTypeFromDocComment($param);
        return array_map(function ($value) use ($itemType) {
            return match ($itemType) {
                'int' => (int)$value,
                'float' => (float)$value,
                'string' => (string)$value,
                'bool' => (bool)$value,
                \DateTime::class => new \DateTime((string)$value),
                \DateTimeImmutable::class => new \DateTimeImmutable((string)$value),
                Ksuid::class => KsuidFactory::fromString((string)$value),
                default => $value,
            };
        }, array_values($args));
    }

    private function getEmbeddedObject(\ReflectionParameter $param, string $path): mixed
    {
        $type = $param->getType();
        if (!$type instanceof \ReflectionNamedType) {
            throw new RuntimeException('Type is union or intersection');
        }

        $args = array_filter($this->args, fn ($key) => str_starts_with($key, $path . '.'), ARRAY_FILTER_USE_KEY);
        $embeddedArgs = array_combine(
            array_map(fn ($key) => substr($key, strlen($path) + 1), array_keys($args)),
            $args
        );

        /** @var class-string $typeName */
        $typeName = $type->getName();
        return (new self())->withRequestType($typeName)->withArgs($embeddedArgs)->build();
    }

    private function getDefaultValue(\ReflectionParameter $param): mixed
    {
        return $param->isDefaultValueAvailable() ? $param->getDefaultValue() : null;
    }
}
