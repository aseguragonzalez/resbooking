<?php

declare(strict_types=1);

namespace App\Application\Projects\ResetCredential;

use App\Domain\Projects\ProjectRepository;
use App\Seedwork\Application\UseCase;
use App\Seedwork\Exceptions\NotImplementedException;

/**
 * @extends UseCase<ResetCredentialRequest>
 */
final class ResetCredential extends UseCase
{
    public function __construct(private readonly ProjectRepository $projectRepository)
    {
    }

    public function execute($request): void
    {
        throw new NotImplementedException();
    }
}
