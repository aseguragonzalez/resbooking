<?php

declare(strict_types=1);

namespace App\Application\Offers\RemoveTurns;

use App\Seedwork\Application\UseCase;
use App\Seedwork\Exception\NotImplementedException;

class RemoveTurns extends UseCase
{
    public function execute(RemoveTurnsRequest $request): void
    {
        throw new NotImplementedException();
    }
}
