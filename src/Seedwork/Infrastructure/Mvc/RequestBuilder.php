<?php

declare(strict_types=1);

namespace Seedwork\Infrastructure\Mvc;

use Tuupola\Ksuid;
use Tuupola\KsuidFactory;

use function PHPUnit\Framework\isInstanceOf;

final class RequestBuilder
{
    /** @var array<string, string> */
    private array $body = [];

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
        $this->requestType = $requestType;

        return $this;
    }

    /**
     * @param array<string, string> $body
     */
    public function withBody(array $body): RequestBuilder
    {
        $this->body = $body;

        return $this;
    }

    public function build(): object
    {
        $reflectionClass = new \ReflectionClass($this->requestType);
        $constructor = $reflectionClass->getConstructor();
        $constructorParameters = $constructor ? $constructor->getParameters() : [];
        $arguments = array_map(
            fn (\ReflectionParameter $param) => array_key_exists($param->getName(), $this->body)
                ? $this->getArgumentValue($param)
                : $this->getDefaultValue($param),
            $constructorParameters
        );
        return $reflectionClass->newInstanceArgs($arguments);
    }

    private function getArgumentValue(\ReflectionParameter $param): mixed
    {
        $name = $param->getName();
        $type = $param->getType();
        $typeName = $type instanceof \ReflectionNamedType ? $type->getName() : 'mixed';
        return match ($typeName) {
            // TODO: add uuid type
            'int' => (int)$this->body[$name],
            'float' => (float)$this->body[$name],
            'string' => (string)$this->body[$name],
            'bool' => (bool)$this->body[$name],
            \DateTime::class => new \DateTime((string)$this->body[$name]),
            \DateTimeImmutable::class => new \DateTimeImmutable((string)$this->body[$name]),
            Ksuid::class => KsuidFactory::fromString((string)$this->body[$name]),
            default => $this->body[$name],
        };
    }

    private function getDefaultValue(\ReflectionParameter $param): mixed
    {
        return $param->isDefaultValueAvailable() ? $param->getDefaultValue() : null;
    }
}
