<?php

declare(strict_types=1);

namespace App\Domain\Users;

use App\Domain\Users\Entities\User;
use App\Domain\Users\ValueObjects\Credential;
use App\Domain\Shared\{Email, Password, Role};

final class UserFactory
{
    public function createNewAdmin(Email $username, Password $password = null): User
    {
        return User::new(username: $username, password: $password, roles: [Role::ADMIN]);
    }

    public function createNewUser(Email $username, Password $password = null): User
    {
        return User::new(username: $username, password: $password, roles: [Role::USER]);
    }
}
