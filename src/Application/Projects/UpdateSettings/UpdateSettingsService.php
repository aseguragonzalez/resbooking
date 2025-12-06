<?php

declare(strict_types=1);

namespace Application\Projects\UpdateSettings;

use Domain\Projects\ProjectRepository;
use Domain\Projects\ValueObjects\Settings;

final class UpdateSettingsService implements UpdateSettings
{
    public function __construct(private readonly ProjectRepository $projectRepository)
    {
    }

    public function execute(UpdateSettingsCommand $command): void
    {
        $project = $this->projectRepository->getById($command->projectId);
        $project->updateSettings(new Settings(
            email: $command->email,
            hasReminders: $command->hasReminders,
            name: $command->name,
            maxNumberOfDiners: $command->maxNumberOfDiners,
            minNumberOfDiners: $command->minNumberOfDiners,
            numberOfTables: $command->numberOfTables,
            phone: $command->phone
        ));
        $this->projectRepository->save($project);
    }
}
