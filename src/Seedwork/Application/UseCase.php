<?php

declare(strict_types=1);

namespace App\Seedwork\Application;

use App\Seedwork\Application\UseCaseRequest;

/**
 * @phpstan-template T of UseCaseRequest
 * @template T of UseCaseRequest
 */
abstract class UseCase
{
    /**
     * @param T $request
     */
    abstract public function execute($request): void;
}
