<?php

declare(strict_types=1);

namespace App\Application\Projects\AddOpenCloseEvent;

use App\Seedwork\Application\UseCase;
use App\Seedwork\Exceptions\NotImplementedException;

class AddOpenCloseEvent extends UseCase
{
    public function execute(AddOpenCloseEventRequest $request): void
    {
        throw new NotImplementedException();
    }
}
