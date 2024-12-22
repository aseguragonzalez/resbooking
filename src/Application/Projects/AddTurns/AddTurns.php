<?php

declare(strict_types=1);

namespace App\Application\Projects\AddTurns;

use App\Application\Projects\AddTurns\AddTurnsRequest;
use App\Domain\Projects\ProjectRepository;
use App\Seedwork\Application\UseCase;

final class AddTurns extends UseCase
{
    public function __construct(private readonly ProjectRepository $projectRepository)
    {
    }

    public function execute(AddTurnsRequest $request): void
    {
        throw new \Exception('Not implemented');
    }
}
