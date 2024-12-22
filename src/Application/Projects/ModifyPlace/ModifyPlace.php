<?php

declare(strict_types=1);

namespace App\Application\Projects\ModifyPlace;

use App\Application\Projects\ModifyPlace\ModifyPlaceRequest;
use App\Domain\Projects\ProjectRepository;
use App\Seedwork\Application\UseCase;

final class ModifyPlace extends UseCase
{
    public function __construct(private readonly ProjectRepository $projectRepository)
    {
    }

    public function execute(ModifyPlaceRequest $request): void
    {
        throw new \Exception('Not implemented');
    }
}
