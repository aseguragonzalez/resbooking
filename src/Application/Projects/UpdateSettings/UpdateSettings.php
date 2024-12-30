<?php

declare(strict_types=1);

namespace App\Application\Projects\UpdateSettings;

use App\Domain\Projects\ProjectRepository;
use App\Seedwork\Application\UseCase;
use App\Seedwork\Exceptions\NotImplementedException;

/**
 * @extends UseCase<UpdateSettingsRequest>
 */
final class UpdateSettings extends UseCase
{
    public function __construct(private readonly ProjectRepository $projectRepository)
    {
    }

    public function execute(UpdateSettingsRequest $request): void
    {
        throw new NotImplementedException();
    }
}
