<?php

declare(strict_types=1);

namespace App\Application\Users\ResetUserCredential;

use Seedwork\Application\UseCaseRequest;

final class ResetUserCredentialRequest extends UseCaseRequest
{
    public function __construct(public readonly string $username)
    {
    }
}
