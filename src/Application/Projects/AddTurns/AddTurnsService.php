<?php

declare(strict_types=1);

namespace Application\Projects\AddTurns;

use Domain\Projects\ProjectRepository;
use Domain\Shared\Capacity;
use Domain\Shared\DayOfWeek;
use Domain\Shared\Turn;
use Domain\Projects\ValueObjects\TurnAvailability;

final class AddTurnsService implements AddTurns
{
    public function __construct(private readonly ProjectRepository $projectRepository)
    {
    }

    public function execute(AddTurnsCommand $command): void
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
