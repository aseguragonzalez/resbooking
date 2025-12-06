<?php

declare(strict_types=1);

namespace Application\Projects\UpdateSettings;

use Domain\Projects\ProjectRepository;
use Domain\Projects\ValueObjects\Settings;
use Seedwork\Application\ApplicationService;

/**
 * @extends ApplicationService<UpdateSettingsCommand>
 */
final class UpdateSettingsService extends ApplicationService implements UpdateSettings
{
    public function __construct(private readonly ProjectRepository $projectRepository)
    {
    }

    /**
     * @param UpdateSettingsCommand $command
     */
    public function execute($command): void
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
