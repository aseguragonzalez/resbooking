<?php

declare(strict_types=1);

namespace App\Application\Offers\RemoveOpenCloseEvent;

use App\Seedwork\Application\UseCase;
use App\Seedwork\Exception\NotImplementedException;

class RemoveOpenCloseEvent extends UseCase
{
    public function execute(RemoveOpenCloseEventRequest $request): void
    {
        throw new NotImplementedException();
    }
}
