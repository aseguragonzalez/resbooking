<?php

declare(strict_types=1);

namespace App\Domain\Projects\Entities;

use App\Domain\Projects\ValueObjects\Credential;
use App\Domain\Shared\{Email, Role};
use App\Seedwork\Domain\Entity;

final class User extends Entity
{
    public function __construct(
        public readonly Email $username,
        private Credential $credential,
        private bool $locked = false,
        private bool $available = true,
        private array $roles = [],
    ) {
        parent::__construct((string) $username);
    }

    public function lock(): void
    {
        $this->locked = true;
    }

    public function unlock(): void
    {
        $this->locked = false;
    }

    public function isLocked(): bool
    {
        return $this->locked;
    }

    public function disable(): void
    {
        $this->available = false;
    }

    public function enable(): void
    {
        $this->available = true;
    }

    public function isAvailable(): bool
    {
        return $this->available;
    }

    public function addRole(Role $role): void
    {
        $this->roles[] = $role;
    }

    public function removeRole(Role $role): void
    {
        $this->roles = array_filter(
            $this->roles,
            fn (Role $r) => $r !== $role
        );
    }

    public function hasRole(Role $role): bool
    {
        return in_array($role, $this->roles, true);
    }

    public function getCredential(): Credential
    {
        return $this->credential;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function changeCredential(Credential $credential): void
    {
        $this->credential = $credential;
    }

    public function equals(Entity $other): bool
    {
        if (!$other instanceof self) {
            return false;
        }

        return parent::equals($other) && $this->username->equals($other->username);
    }

    public static function createNewAdmin(Email $username, Credential $credential): self
    {
        return new self($username, $credential, false, true, [Role::ADMIN]);
    }

    public static function createNewUser(Email $username, Credential $credential): self
    {
        return new self($username, $credential, false, true, [Role::USER]);
    }
}
