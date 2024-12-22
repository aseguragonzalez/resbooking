<?php

declare(strict_types=1);

namespace App\Application\Projects\AddPlace;

use App\Application\Projects\AddPlace\AddPlaceRequest;
use App\Domain\Projects\ProjectRepository;
use App\Seedwork\Application\UseCase;

final class AddPlace extends UseCase
{
    public function __construct(private readonly ProjectRepository $projectRepository)
    {
    }

    public function execute(AddPlaceRequest $request): void
    {
        throw new \Exception('Not implemented');
    }
}
