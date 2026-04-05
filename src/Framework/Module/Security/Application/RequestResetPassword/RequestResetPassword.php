<?php

declare(strict_types=1);

namespace Framework\Security\Application\RequestResetPassword;

interface RequestResetPassword
{
    public function execute(RequestResetPasswordCommand $command): void;
}
