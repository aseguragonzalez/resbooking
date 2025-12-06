<?php

declare(strict_types=1);

namespace Application\Projects\AddTurns;

use Domain\Projects\ProjectRepository;
use Domain\Shared\ValueObjects\TurnAvailability;
use Domain\Shared\Capacity;
use Domain\Shared\DayOfWeek;
use Domain\Shared\Turn;
use Seedwork\Application\ApplicationService;

/**
 * @extends ApplicationService<AddTurnsCommand>
 */
final class AddTurnsService extends ApplicationService implements AddTurns
{
    public function __construct(private readonly ProjectRepository $projectRepository)
    {
    }

    /**
     * @param AddTurnsCommand $command
     */
    public function execute($command): void
    {
        $project = $this->projectRepository->getById($command->projectId);
        foreach ($command->turns as $turn) {
            $turnAvailability = new TurnAvailability(
                dayOfWeek: DayOfWeek::getById($turn->dayOfWeek),
                capacity: new Capacity($turn->capacity),
                turn: Turn::getByStartTime($turn->startTime)
            );
            $project->addTurn($turnAvailability);
        }
        $this->projectRepository->save($project);
    }
}
