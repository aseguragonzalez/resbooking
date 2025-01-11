<?php

declare(strict_types=1);

namespace App\Application\Projects\AddTurns;

use App\Domain\Projects\ProjectRepository;
use App\Domain\Shared\{Capacity, DayOfWeek, Turn};
use App\Domain\Shared\ValueObjects\TurnAvailability;
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

    public function execute($request): void
    {
        $project = $this->projectRepository->getById($request->projectId);
        foreach ($request->turns as $turn) {
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
