<?php

declare(strict_types=1);

namespace App\Application\Users\ChangeUserCredential;

use Seedwork\Application\UseCaseRequest;

final class ChangeUserCredentialRequest extends UseCaseRequest
{
    public function __construct(public readonly string $username, public readonly string $password)
    {
    }
}
