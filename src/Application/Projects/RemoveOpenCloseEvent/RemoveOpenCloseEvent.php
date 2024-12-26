<?php

declare(strict_types=1);

namespace App\Application\Projects\RemoveOpenCloseEvent;

use App\Seedwork\Application\UseCase;
use App\Seedwork\Exceptions\NotImplementedException;

class RemoveOpenCloseEvent extends UseCase
{
    public function execute(RemoveOpenCloseEventRequest $request): void
    {
        throw new NotImplementedException();
    }
}
