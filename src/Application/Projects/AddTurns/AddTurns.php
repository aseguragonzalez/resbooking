<?php

declare(strict_types=1);

namespace App\Application\Projects\AddTurns;

use App\Domain\Projects\ProjectRepository;
use App\Domain\Shared\ValueObjects\TurnAvailability;
use App\Domain\Shared\{Capacity, DayOfWeek, Turn};
use Seedwork\Application\ApplicationService;

/**
 * @extends ApplicationService<AddTurnsCommand>
 */
final class AddTurns extends ApplicationService
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
            $project->AddTurn($turnAvailability);
        }
        $this->projectRepository->save($project);
    }
}
