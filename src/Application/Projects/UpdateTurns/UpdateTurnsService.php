<?php

declare(strict_types=1);

namespace Application\Projects\UpdateTurns;

use Domain\Projects\Repositories\ProjectRepository;
use Domain\Projects\Services\ProjectObtainer;
use Domain\Projects\ValueObjects\TurnAvailability;
use Domain\Shared\Capacity;
use Domain\Shared\DayOfWeek;
use Domain\Shared\Turn;

final readonly class UpdateTurnsService implements UpdateTurns
{
    public function __construct(
        private ProjectObtainer $projectObtainer,
        private ProjectRepository $projectRepository,
    ) {
    }

    public function execute(UpdateTurnsCommand $command): void
    {
        $project = $this->projectObtainer->obtain(id: $command->projectId);

        /** @var array<TurnAvailability> */
        $turns = array_map(
            fn ($turnAvailabilityData) => new TurnAvailability(
                capacity: new Capacity($turnAvailabilityData->capacity),
                dayOfWeek: DayOfWeek::getById($turnAvailabilityData->dayOfWeekId),
                turn: Turn::getById($turnAvailabilityData->turnId),
            ),
            $command->turns
        );

        $project->updateTurns($turns);
        $this->projectRepository->save($project);
    }
}
