<?php

declare(strict_types=1);

namespace Seedwork\Infrastructure\Mvc\Security\Domain\Exceptions;

final class ResetPasswordChallengeException extends \Exception
{
    public function __construct(string $token = "")
    {
        parent::__construct("Invalid or expired reset password token: {$token}");
    }
}
