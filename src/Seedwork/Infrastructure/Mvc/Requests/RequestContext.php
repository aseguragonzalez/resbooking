<?php

declare(strict_types=1);

namespace Seedwork\Infrastructure\Mvc\Requests;

final class RequestContext
{
    /**
     * @param array<string, mixed> $keys
     */
    public function __construct(private array $keys = [])
    {
    }

    public function get(string $key): string
    {
        $this->checkIfKeyExists($key);

        if (!is_string($this->keys[$key])) {
            throw new \RuntimeException("Value for key '{$key}' is not a string");
        }
        return (string)$this->keys[$key];
    }

    private function checkIfKeyExists(string $key): void
    {
        if (!array_key_exists($key, $this->keys)) {
            throw new \RuntimeException("Key '{$key}' not found");
        }
    }

    /**
     * @template T
     * @param string $key
     * @param class-string<T> $type
     * @return T
     */
    public function getAs(string $key, string $type): mixed
    {
        $this->checkIfKeyExists($key);

        if (!($this->keys[$key] instanceof $type)) {
            throw new \RuntimeException("Value for key '{$key}' is not of type '{$type}'");
        }
        return $this->keys[$key];
    }

    public function set(string $key, mixed $value): void
    {
        $this->keys[$key] = $value;
    }
}
