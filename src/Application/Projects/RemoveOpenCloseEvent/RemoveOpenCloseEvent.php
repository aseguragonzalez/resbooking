<?php

declare(strict_types=1);

namespace App\Application\Projects\RemoveOpenCloseEvent;

use App\Domain\Projects\ProjectRepository;
use App\Seedwork\Application\UseCase;
use App\Seedwork\Exceptions\NotImplementedException;

/**
 * @extends UseCase<RemoveOpenCloseEventRequest>
 */
class RemoveOpenCloseEvent extends UseCase
{
    public function __construct(private readonly ProjectRepository $projectRepository)
    {
    }

    public function execute(RemoveOpenCloseEventRequest $request): void
    {
        throw new NotImplementedException();
    }
}
