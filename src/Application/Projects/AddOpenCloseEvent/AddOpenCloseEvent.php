<?php

declare(strict_types=1);

namespace App\Application\Projects\AddOpenCloseEvent;

use App\Domain\Projects\ProjectRepository;
use App\Seedwork\Application\UseCase;
use App\Seedwork\Exceptions\NotImplementedException;

/**
 * @extends UseCase<AddOpenCloseEventRequest>
 */
class AddOpenCloseEvent extends UseCase
{
    public function __construct(private readonly ProjectRepository $projectRepository)
    {
    }

    public function execute(AddOpenCloseEventRequest $request): void
    {
        throw new NotImplementedException();
    }
}
