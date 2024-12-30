<?php

declare(strict_types=1);

namespace App\Application\Projects\RemovePlace;

use App\Domain\Projects\ProjectRepository;
use App\Seedwork\Application\UseCase;
use App\Seedwork\Exceptions\NotImplementedException;

/**
 * @extends UseCase<RemovePlaceRequest>
 */
final class RemovePlace extends UseCase
{
    public function __construct(private readonly ProjectRepository $projectRepository)
    {
    }

    public function execute(RemovePlaceRequest $request): void
    {
        throw new NotImplementedException();
    }
}
