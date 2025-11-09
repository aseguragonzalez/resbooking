<?php

declare(strict_types=1);

namespace App\Application\Projects\RemoveTurn;

use App\Domain\Projects\ProjectRepository;
use App\Domain\Shared\ValueObjects\TurnAvailability;
use Seedwork\Application\ApplicationService;

/**
 * @template-extends ApplicationService<RemoveTurnCommand>
 * @extends ApplicationService<RemoveTurnCommand>
 */
final class RemoveTurn extends ApplicationService
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
