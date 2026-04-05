<?php

declare(strict_types=1);

namespace Framework\Module\Security\Application\RequestResetPassword;

interface RequestResetPassword
{
    public function execute(RequestResetPasswordCommand $command): void;
}
