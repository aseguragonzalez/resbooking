<?php

declare(strict_types=1);

namespace App\Application\Projects\UpdateCredential;

use App\Application\Projects\UpdateCredential\UpdateCredentialRequest;
use App\Domain\Projects\ProjectRepository;
use App\Seedwork\Application\UseCase;

final class UpdateCredential extends UseCase
{
    public function __construct(private readonly ProjectRepository $projectRepository)
    {
    }

    public function execute(UpdateCredentialRequest $request): void
    {
        throw new \Exception('Not implemented');
    }
}
