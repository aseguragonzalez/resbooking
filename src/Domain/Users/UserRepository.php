<?php

declare(strict_types=1);

namespace App\Domain\Users;

use App\Seedwork\Domain\Repository;

/**
 * @extends Repository<User>
 */
interface UserRepository extends Repository
{
}
