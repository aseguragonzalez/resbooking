<?php

declare(strict_types=1);

namespace App\Application\Projects\RemoveUser;

use App\Application\Projects\RemoveUser\RemoveUserRequest;
use App\Domain\Projects\ProjectRepository;
use App\Seedwork\Application\UseCase;

final class RemoveUser extends UseCase
{
    public function __construct(private readonly ProjectRepository $projectRepository)
    {
    }

    public function execute(RemoveUserRequest $request): void
    {
        throw new \Exception('Not implemented');
    }
}
