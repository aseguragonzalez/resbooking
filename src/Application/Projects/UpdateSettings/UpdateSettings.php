<?php

declare(strict_types=1);

namespace App\Application\Projects\UpdateSettings;

use App\Domain\Projects\ProjectRepository;
use App\Domain\Projects\ValueObjects\Settings;
use Seedwork\Application\ApplicationService;

/**
 * @template-extends ApplicationService<UpdateSettingsCommand>
 * @extends ApplicationService<UpdateSettingsCommand>
 */
final class UpdateSettings extends ApplicationService
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
            hasRemainders: $command->hasRemainders,
            name: $command->name,
            maxNumberOfDiners: $command->maxNumberOfDiners,
            minNumberOfDiners: $command->minNumberOfDiners,
            numberOfTables: $command->numberOfTables,
            phone: $command->phone
        ));
        $this->projectRepository->save($project);
    }
}
