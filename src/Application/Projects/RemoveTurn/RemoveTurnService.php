<?php

declare(strict_types=1);

namespace Application\Projects\RemoveTurn;

use Domain\Projects\ProjectRepository;
use Domain\Shared\ValueObjects\TurnAvailability;
use Seedwork\Application\ApplicationService;

/**
 * @extends ApplicationService<RemoveTurnCommand>
 */
final class RemoveTurnService extends ApplicationService implements RemoveTurn
{
    public function __construct(private readonly ProjectRepository $projectRepository)
    {
    }

    /**
     * @param RemoveTurnCommand $command
     */
    public function execute($command): void
    {
        $project = $this->projectRepository->getById($command->projectId);
        $project->removeTurns(
            fn (TurnAvailability $turn) => $turn->dayOfWeek === $command->dayOfWeek && $turn->turn === $command->turn
        );
        $this->projectRepository->save($project);
    }
}
