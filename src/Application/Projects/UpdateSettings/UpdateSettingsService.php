<?php

declare(strict_types=1);

namespace Application\Projects\UpdateSettings;

use Domain\Projects\Repositories\ProjectRepository;
use Domain\Projects\Services\ProjectObtainer;
use Domain\Projects\ValueObjects\Settings;
use Domain\Shared\Capacity;
use Domain\Shared\Email;
use Domain\Shared\Phone;

final readonly class UpdateSettingsService implements UpdateSettings
{
    public function __construct(
        private ProjectObtainer $projectObtainer,
        private ProjectRepository $projectRepository,
    ) {
    }

    public function execute(UpdateSettingsCommand $command): void
    {
        $project = $this->projectObtainer->obtain(id: $command->projectId);
        $project->updateSettings(new Settings(
            email: new Email($command->email),
            hasReminders: $command->hasReminders,
            name: $command->name,
            maxNumberOfDiners: new Capacity($command->maxNumberOfDiners),
            minNumberOfDiners: new Capacity($command->minNumberOfDiners),
            numberOfTables: new Capacity($command->numberOfTables),
            phone: new Phone($command->phone)
        ));
        $this->projectRepository->save($project);
    }
}
