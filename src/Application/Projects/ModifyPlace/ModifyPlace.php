<?php

declare(strict_types=1);

namespace App\Application\Projects\ModifyPlace;

use App\Domain\Projects\ProjectRepository;
use App\Seedwork\Application\UseCase;
use App\Seedwork\Exceptions\NotImplementedException;

/**
 * @extends UseCase<ModifyPlaceRequest>
 */
final class ModifyPlace extends UseCase
{
    public function __construct(private readonly ProjectRepository $projectRepository)
    {
    }

    public function execute(ModifyPlaceRequest $request): void
    {
        throw new NotImplementedException();
    }
}
