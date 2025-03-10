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
                    fn ($key) => str_starts_with($key, $param->getName() . '.'),
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
            default => $type->isBuiltin() ? $this->args[$name] : $this->getEmbeddedObject($param, $name),
        };
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
