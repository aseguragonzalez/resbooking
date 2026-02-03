<?php

declare(strict_types=1);

namespace Framework\Mvc\Security\Application\RequestResetPassword;

interface RequestResetPassword
{
    public function execute(RequestResetPasswordCommand $command): void;
}
