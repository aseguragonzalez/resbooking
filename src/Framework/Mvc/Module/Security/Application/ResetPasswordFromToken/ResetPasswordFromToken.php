<?php

declare(strict_types=1);

namespace Framework\Mvc\Security\Application\ResetPasswordFromToken;

interface ResetPasswordFromToken
{
    public function execute(ResetPasswordFromTokenCommand $command): void;
}
