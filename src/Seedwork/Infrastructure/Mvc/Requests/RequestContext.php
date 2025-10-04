<?php

declare(strict_types=1);

namespace Seedwork\Infrastructure\Mvc\Requests;

use Seedwork\Infrastructure\Mvc\Security\Identity;

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

    public function setIdentity(Identity $identity): void
    {
        $this->keys['identity'] = $identity;
    }

    /**
     * @return Identity
     */
    public function getIdentity(): Identity
    {
        $this->checkIfKeyExists('identity');
        /** @var Identity */
        $identity = $this->keys['identity'];
        return $identity;
    }

    public function setIdentityToken(string $token): void
    {
        $this->keys['identity_token'] = $token;
    }

    /**
     * @return string
     */
    public function getIdentityToken(): string
    {
        $this->checkIfKeyExists('identity_token');

        if (!is_string($this->keys['identity_token'])) {
            throw new \RuntimeException("Value for key 'identity_token' is not of type 'string'");
        }

        return $this->keys['identity_token'];
    }
}
