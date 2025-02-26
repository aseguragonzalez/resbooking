<?php

declare(strict_types=1);

namespace App\Application\Projects\UpdateSettings;

use App\Domain\Projects\ProjectRepository;
use App\Domain\Projects\ValueObjects\Settings;
use App\Seedwork\Application\UseCase;

/**
 * @template-extends UseCase<UpdateSettingsRequest>
 * @extends UseCase<UpdateSettingsRequest>
 */
final class UpdateSettings extends UseCase
{
    public function __construct(private readonly ProjectRepository $projectRepository)
    {
    }

    /**
     * @param UpdateSettingsRequest $request
     */
    public function execute($request): void
    {
        $project = $this->projectRepository->getById($request->projectId);
        $project->updateSettings(new Settings(
            email: $request->email,
            hasRemainders: $request->hasRemainders,
            name: $request->name,
            maxNumberOfDiners: $request->maxNumberOfDiners,
            minNumberOfDiners: $request->minNumberOfDiners,
            numberOfTables: $request->numberOfTables,
            phone: $request->phone
        ));
        $this->projectRepository->save($project);
    }
}
