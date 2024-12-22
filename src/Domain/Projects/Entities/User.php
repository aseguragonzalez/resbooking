<?php

declare(strict_types=1);

namespace App\Domain\Projects\Entities;

use App\Domain\Projects\Credential;
use App\Domain\Shared\Role;
use App\Seedwork\Domain\Entity;

final class User extends Entity
{
    public function __construct(
        private readonly string $username,
        private Credential $credential,
        private bool $locked = false,
        private bool $available = true,
        private array $roles = [],
    ) {
        parent::__construct($username);
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
        $this->roles = array_filter($this->roles, fn(Role $r) => $r->equals($role));
    }

    public function hasRole(Role $role): bool
    {
        return in_array($role, $this->roles, true);
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function changeCredential(Credential $credential): void
    {
        $this->credential = $credential;
    }

    public function signin(string $password): void
    {
        if (!$this->credential->check($password))
        {
            throw new Exception("authentication");
        }
    }

    public function equals(User $other): bool
    {
        return $this->username === $other->username;
    }

    public static function createNewAdmin(string $username, Credential $credential): Self
    {
        return new Self($username, $credential, false, true, [Role::ADMIN]);
    }

    public static function createNewUser(string $username, Credential $credential): Self
    {
        return new Self($username, $credential, false, true, [Role::USER]);
    }
}
