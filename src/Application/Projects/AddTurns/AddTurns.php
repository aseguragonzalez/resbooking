<?php

declare(strict_types=1);

namespace App\Application\Projects\AddTurns;

use App\Domain\Projects\ProjectRepository;
use App\Seedwork\Application\UseCase;
use App\Seedwork\Exceptions\NotImplementedException;

/**
 * @extends UseCase<AddOpenCloseEventRequest>
 */
final class AddTurns extends UseCase
{
    public function __construct(private readonly ProjectRepository $projectRepository)
    {
    }

    public function execute(AddTurnsRequest $request): void
    {
        throw new NotImplementedException();
    }
}
