<?php

declare(strict_types=1);

namespace App\Domain\Users\Entities;

use App\Domain\Shared\Exceptions\{RoleAlreadyExist, RoleDoesNotExist};
use App\Domain\Users\ValueObjects\Credential;
use App\Domain\Shared\{Email, Role, Password};
use App\Seedwork\Domain\AggregateRoot;

final class User extends AggregateRoot
{
    /**
     * @param array<Role> $roles An array representing the roles assigned to the user.
     */
    private function __construct(
        public readonly Email $username,
        private Credential $credential,
        private bool $locked = false,
        private bool $available = true,
        private array $roles = [],
    ) {
        parent::__construct((string) $username);
    }

    public static function build(
        Email $username,
        Credential $credential,
        bool $locked = false,
        bool $available = true,
        array $roles = [],
    ): self {
        return new self($username, $credential, $locked, $available, $roles);
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
        if (in_array($role, $this->roles, true)) {
            throw new RoleAlreadyExist();
        }
        $this->roles[] = $role;
    }

    public function removeRole(Role $role): void
    {
        if (!in_array($role, $this->roles, true)) {
            throw new RoleDoesNotExist();
        }
        $this->roles = array_filter(
            $this->roles,
            fn (Role $r) => $r != $role
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

    /**
     * @return array<Role>
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    public function changeCredential(Credential $credential): void
    {
        $this->credential = $credential;
    }

    public static function createNewAdmin(Email $username, Password $password): self
    {
        $credential = Credential::new(password: $password);

        return new self($username, $credential, false, true, [Role::ADMIN]);
    }

    public static function createNewUser(Email $username, Password $password): self
    {
        $credential = Credential::new(password: $password);

        return new self($username, $credential, false, true, [Role::USER]);
    }
}
