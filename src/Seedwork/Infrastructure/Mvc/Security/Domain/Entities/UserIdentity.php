<?php

declare(strict_types=1);

namespace Seedwork\Infrastructure\Mvc\Security\Domain\Entities;

use Seedwork\Infrastructure\Mvc\Security\Identity;
use Seedwork\Infrastructure\Mvc\Security\Domain\Exceptions\{
    InvalidCredentialsException,
    UserBlockedException,
    UserIsNotActiveException,
    UsernameIsNotEmailException
};

final class UserIdentity implements Identity
{
    /**
     * @param array<string> $roles
     */
    private function __construct(
        public readonly string $hash1,
        public readonly string $hash2,
        public readonly array $roles,
        public readonly string $seed,
        private readonly string $username,
        public readonly bool $isActive = false,
        public readonly bool $isAuthenticated = false,
        public readonly bool $isBlocked = false
    ) {
        if (($this->username !== 'anonymous') && !filter_var($this->username, FILTER_VALIDATE_EMAIL)) {
            throw new UsernameIsNotEmailException($this->username);
        }
    }

    public function isAuthenticated(): bool
    {
        return $this->isAuthenticated;
    }

    /**
     * @return array<string>
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    public function hasRole(string $role): bool
    {
        return in_array($role, $this->roles);
    }

    public function username(): string
    {
        return $this->username;
    }

    public function activate(): self
    {
        return new self(
            hash1: $this->hash1,
            hash2: $this->hash2,
            roles: $this->roles,
            seed: $this->seed,
            username: $this->username,
            isActive: true,
            isAuthenticated: $this->isAuthenticated,
            isBlocked: $this->isBlocked
        );
    }

    public function deactivate(): self
    {
        return new self(
            hash1: $this->hash1,
            hash2: $this->hash2,
            roles: $this->roles,
            seed: $this->seed,
            username: $this->username,
            isActive: false,
            isAuthenticated: $this->isAuthenticated,
            isBlocked: $this->isBlocked
        );
    }

    public function block(): self
    {
        return new self(
            hash1: $this->hash1,
            hash2: $this->hash2,
            roles: $this->roles,
            seed: $this->seed,
            username: $this->username,
            isActive: $this->isActive,
            isAuthenticated: $this->isAuthenticated,
            isBlocked: true
        );
    }

    public function unblock(): self
    {
        return new self(
            hash1: $this->hash1,
            hash2: $this->hash2,
            roles: $this->roles,
            seed: $this->seed,
            username: $this->username,
            isActive: $this->isActive,
            isAuthenticated: $this->isAuthenticated,
            isBlocked: false
        );
    }

    public function validatePassword(string $password): void
    {
        $hash1 = self::getHash1FromPassword($password, $this->seed);
        $hash2 = self::getHash2FromPassword($password, $this->seed);

        if ($this->hash1 === $hash1 && $this->hash2 === $hash2) {
            return;
        }

        throw new InvalidCredentialsException($this->username);
    }

    public function authenticate(string $password): UserIdentity
    {
        $this->validatePassword($password);

        if ($this->isAuthenticated) {
            return $this;
        }

        if (!$this->isActive) {
            throw new UserIsNotActiveException($this->username);
        }

        if ($this->isBlocked) {
            throw new UserBlockedException($this->username);
        }

        return new self(
            hash1: $this->hash1,
            hash2: $this->hash2,
            roles: $this->roles,
            seed: $this->seed,
            username: $this->username,
            isActive: $this->isActive,
            isAuthenticated: true,
            isBlocked: $this->isBlocked
        );
    }

    public function updatePassword(string $newPassword): self
    {
        if (!$this->isActive) {
            throw new UserIsNotActiveException($this->username);
        }

        if ($this->isBlocked) {
            throw new UserBlockedException($this->username);
        }

        $seed = self::getSeedFromPassword();
        return new self(
            hash1: self::getHash1FromPassword($newPassword, $seed),
            hash2: self::getHash2FromPassword($newPassword, $seed),
            roles: $this->roles,
            seed: $seed,
            username: $this->username,
            isActive: $this->isActive,
            isAuthenticated: $this->isAuthenticated,
            isBlocked: $this->isBlocked
        );
    }

    /**
     * @param array<string> $newRoles
     */
    public function updateRoles(array $newRoles): self
    {
        return new self(
            hash1: $this->hash1,
            hash2: $this->hash2,
            roles: $newRoles,
            seed: $this->seed,
            username: $this->username,
            isActive: $this->isActive,
            isAuthenticated: $this->isAuthenticated,
            isBlocked: $this->isBlocked
        );
    }

    public function getCurrentIdentity(): Identity
    {
        return CurrentIdentity::build(
            isAuthenticated: $this->isAuthenticated,
            roles: $this->roles,
            username: $this->username,
        );
    }

    public static function anonymous(): self
    {
        return new self(
            hash1: '',
            hash2: '',
            roles: [],
            seed: '',
            username: 'anonymous',
            isActive: false,
            isAuthenticated: false,
            isBlocked: false
        );
    }

    /**
     * @param array<string> $roles
     */
    public static function build(
        string $hash1,
        string $hash2,
        array $roles,
        string $seed,
        string $username,
        bool $isActive,
        bool $isBlocked
    ): self {
        return new self(
            hash1: $hash1,
            hash2: $hash2,
            roles: $roles,
            seed: $seed,
            username: $username,
            isActive: $isActive,
            isAuthenticated: false,
            isBlocked: $isBlocked
        );
    }

    /**
     * @param array<string> $roles
     */
    public static function new(string $username, array $roles, string $password): self
    {
        $seed = self::getSeedFromPassword();
        return new self(
            hash1: self::getHash1FromPassword($password, $seed),
            hash2: self::getHash2FromPassword($password, $seed),
            roles: $roles,
            seed: $seed,
            username: $username,
            isActive: false,
            isAuthenticated: false,
            isBlocked: false
        );
    }

    private static function getSeedFromPassword(): string
    {
        return bin2hex(random_bytes(16));
    }

    private static function getHash1FromPassword(string $password, string $seed): string
    {
        return hash('sha256', $password . $seed);
    }

    private static function getHash2FromPassword(string $password, string $seed): string
    {
        return hash('sha512', $password . $seed);
    }
}
