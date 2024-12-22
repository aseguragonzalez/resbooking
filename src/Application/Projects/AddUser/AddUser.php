<?php

declare(strict_types=1);

namespace App\Application\Projects\AddUser;

use App\Application\Projects\AddUser\AddUserRequest;
use App\Domain\Projects\ProjectRepository;
use App\Seedwork\Application\UseCase;

final class AddUser extends UseCase
{
    public function __construct(private readonly ProjectRepository $projectRepository)
    {
    }

    public function execute(AddUserRequest $request): void
    {
        throw new \Exception('Not implemented');
    }
}
