<?php

declare(strict_types=1);

namespace App\Application\Projects\UpdateSettings;

use App\Application\Projects\UpdateSettings\UpdateSettingsRequest;
use App\Domain\Projects\ProjectRepository;
use App\Seedwork\Application\UseCase;

final class UpdateSettings extends UseCase
{
    public function __construct(private readonly ProjectRepository $projectRepository)
    {
    }

    public function execute(UpdateSettingsRequest $request): void
    {
        throw new \Exception('Not implemented');
    }
}
