<?php

declare(strict_types=1);

namespace App\Domain\Users;

use App\Domain\Users\Entities\User;
use App\Domain\Users\ValueObjects\Credential;
use App\Domain\Shared\{Email, Password, Role};

final class UserFactory
{
    public function createNewAdmin(Email $username, Password $password): User
    {
        $credential = Credential::new(password: $password);

        return User::build(username: $username, credential: $credential, roles: [Role::ADMIN]);
    }

    public function createNewUser(Email $username, Password $password): User
    {
        $credential = Credential::new(password: $password);

        return User::build(username: $username, credential: $credential, roles: [Role::USER]);
    }
}
