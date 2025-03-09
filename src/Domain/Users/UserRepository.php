<?php

declare(strict_types=1);

namespace App\Domain\Users;

use Seedwork\Domain\Repository;
use App\Domain\Users\Entities\User;

/**
 * @extends Repository<User>
 */
interface UserRepository extends Repository
{
}
