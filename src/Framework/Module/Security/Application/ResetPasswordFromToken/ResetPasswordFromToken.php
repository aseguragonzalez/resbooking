<?php

declare(strict_types=1);

namespace Framework\Security\Application\ResetPasswordFromToken;

interface ResetPasswordFromToken
{
    public function execute(ResetPasswordFromTokenCommand $command): void;
}
