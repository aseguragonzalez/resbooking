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
     * @param T $request
     * @return void
     */
    abstract public function execute(T $request): void;
}
