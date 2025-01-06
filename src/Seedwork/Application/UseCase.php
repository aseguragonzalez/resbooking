<?php

declare(strict_types=1);

namespace App\Seedwork\Application;

use App\Seedwork\Application\UseCaseRequest;

/**
 * @template T of UseCaseRequest
 */
abstract class UseCase
{
    /**
     * @param UseCaseRequest $request
     * @return void
     */
    public function execute(UseCaseRequest $request): void;
}
