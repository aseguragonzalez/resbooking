<?php

declare(strict_types=1);

namespace App\Application\Projects\RemoveTurns;

use App\Application\Projects\RemoveTurns\RemoveTurnsRequest;
use App\Domain\Projects\ProjectRepository;
use App\Seedwork\Application\UseCase;

final class RemoveTurns extends UseCase
{
    public function __construct(private readonly ProjectRepository $projectRepository)
    {
    }

    public function execute(RemoveTurnsRequest $request): void
    {
        throw new \Exception('Not implemented');
    }
}
