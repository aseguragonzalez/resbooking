<?php

declare(strict_types=1);

namespace App\Domain\Users\Entities;

use App\Domain\Users\Events\{
    UserCreated,
    UserLocked,
    UserUnlocked,
    UserDisabled,
    UserEnabled,
    RoleAddedToUser,
    RoleRemovedFromUser,
    CredentialReset,
    CredentialUpdated
};
use App\Domain\Users\Exceptions\{
    RoleAlreadyExist,
    RoleDoesNotExist,
    UserAlreadyDisabled,
    UserAlreadyEnabled,
    UserAlreadyLocked,
    UserAlreadyUnlocked
};
use App\Domain\Users\ValueObjects\Credential;
use App\Domain\Shared\{Email, Role, Password};
use App\Seedwork\Domain\AggregateRoot;

final class User extends AggregateRoot
{
    /**
     * @param array<Role> $roles An array representing the roles assigned to the user.
     * @param array<DomainEvent> $domainEvents An array representing the domain events.
     */
    private function __construct(
        public readonly Email $username,
        private Credential $credential,
        private bool $locked = false,
        private bool $available = true,
        private array $roles = [],
        array $domainEvents = []
    ) {
        parent::__construct((string) $username, domainEvents: $domainEvents);
    }

    public static function new(Email $username, Password $password = null, array $roles = []): self
    {
        $password = $password ?? Password::new();
        return new self(
            username: $username,
            credential: Credential::new(password: $password),
            roles: $roles,
            domainEvents: [
                UserCreated::new(username: $username->getValue(), roles: $roles, password: $password)
            ]
        );
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
        if ($this->locked) {
            throw new UserAlreadyLocked();
        }
        $this->locked = true;
        $this->addEvent(UserLocked::new(username: $this->username->getValue(), user: $this));
    }

    public function unlock(): void
    {
        if (!$this->locked) {
            throw new UserAlreadyUnlocked();
        }
        $this->locked = false;
        $this->addEvent(UserUnlocked::new(username: $this->username->getValue(), user: $this));
    }

    public function isLocked(): bool
    {
        return $this->locked;
    }

    public function disable(): void
    {
        if (!$this->available) {
            throw new UserAlreadyDisabled();
        }
        $this->available = false;
        $this->addEvent(UserDisabled::new(username: $this->username->getValue(), user: $this));
    }

    public function enable(): void
    {
        if ($this->available) {
            throw new UserAlreadyEnabled();
        }
        $this->available = true;
        $this->addEvent(UserEnabled::new(username: $this->username->getValue(), user: $this));
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
        $this->addEvent(RoleAddedToUser::new(username: $this->username->getValue(), role: $role));
    }

    public function removeRole(Role $role): void
    {
        if (!in_array($role, $this->roles, true)) {
            throw new RoleDoesNotExist();
        }
        $this->roles = array_filter($this->roles, fn (Role $r) => $r != $role);
        $this->addEvent(RoleRemovedFromUser::new(username: $this->username->getValue(), role: $role));
    }

    public function hasRole(Role $role): bool
    {
        return in_array($role, $this->roles, true);
    }

    /**
     * @return array<Role>
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    public function changeCredential(Password $password): void
    {
        $this->credential = Credential::new(password: $password);
        $this->addEvent(CredentialUpdated::new(username: $this->username->getValue()));
    }

    public function getCredential(): Credential
    {
        return $this->credential;
    }

    public function resetCredential(): void
    {
        $password = Password::new();
        $this->credential = Credential::new(password: $password);
        $this->addEvent(
            CredentialReset::new(username: $this->username->getValue(), password: $password)
        );
    }
}
