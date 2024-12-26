<?php

declare(strict_types=1);

namespace App\Application\Offers\AddTurns;

use App\Seedwork\Application\UseCase;
use App\Seedwork\Exceptions\NotImplementedException;

class AddTurns extends UseCase
{
    public function execute(AddTurnsRequest $request): void
    {
        throw new NotImplementedException();
    }
}
