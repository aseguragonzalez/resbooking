<?php

declare(strict_types=1);

namespace Application\Projects\RemoveTurn;

use Domain\Projects\ProjectRepository;
use Domain\Shared\ValueObjects\TurnAvailability;

final class RemoveTurnService implements RemoveTurn
{
    public function __construct(private readonly ProjectRepository $projectRepository)
    {
    }

    public function execute(RemoveTurnCommand $command): void
    {
        $project = $this->projectRepository->getById($command->projectId);
        $project->removeTurns(
            fn (TurnAvailability $turn) => $turn->dayOfWeek === $command->dayOfWeek && $turn->turn === $command->turn
        );
        $this->projectRepository->save($project);
    }
}
