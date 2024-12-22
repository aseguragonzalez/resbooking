<?php

declare(strict_types=1);

namespace App\Seedwork\Application;

use App\Seedwork\Application\UseCaseRequest;
use App\Seedwork\Exceptions\NotImplementedException;

abstract class UseCase
{
    public function execute(UseCaseRequest $request): void
    {
        throw new NotImplementedException();
    }
}
