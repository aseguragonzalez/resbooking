<?php

declare(strict_types=1);

namespace App\Application\Projects\AddTurns;

use App\Domain\Projects\ProjectRepository;
use App\Domain\Shared\ValueObjects\TurnAvailability;
use App\Domain\Shared\{Capacity, DayOfWeek, Turn};
use Seedwork\Application\UseCase;

/**
 * @extends UseCase<AddTurnsRequest>
 */
final class AddTurns extends UseCase
{
    public function __construct(private readonly ProjectRepository $projectRepository)
    {
    }

    /**
     * @param AddTurnsRequest $request
     */
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
