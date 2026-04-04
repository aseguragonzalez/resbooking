<?php

declare(strict_types=1);

namespace Framework\Mvc\Security\Application\ModifyUserIdentityPassword;

interface ModifyUserIdentityPassword
{
    public function execute(ModifyUserIdentityPasswordCommand $command): void;
}
