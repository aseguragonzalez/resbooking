<?php

declare(strict_types=1);

namespace App\Domain\Users;

use App\Domain\Shared\{Email, Password, Role};
use App\Domain\Users\Entities\User;

final class UserFactory
{
    public function createNewAdmin(Email $username, Password $password = null): User
    {
        return User::new(username: $username, password: $password, roles: [Role::Admin]);
    }

    public function createNewUser(Email $username, Password $password = null): User
    {
        return User::new(username: $username, password: $password, roles: [Role::User]);
    }
}
