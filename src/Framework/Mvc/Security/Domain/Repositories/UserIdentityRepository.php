<?php

declare(strict_types=1);

namespace Framework\Mvc\Security\Domain\Repositories;

use Framework\Mvc\Security\Domain\Entities\UserIdentity;

interface UserIdentityRepository
{
    public function save(UserIdentity $user): void;

    public function getByUsername(string $username): ?UserIdentity;

    public function existsByUsername(string $username): bool;
}
