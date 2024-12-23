<?php

declare(strict_types=1);

namespace App\Application\Projects\ResetCredential;

use App\Application\Projects\ResetCredential\ResetCredentialRequest;
use App\Domain\Projects\ProjectRepository;
use App\Seedwork\Application\UseCase;

final class ResetCredential extends UseCase
{
    public function __construct(private readonly ProjectRepository $projectRepository)
    {
    }

    public function execute(ResetCredentialRequest $request): void
    {
        throw new \Exception('Not implemented');
    }
}
