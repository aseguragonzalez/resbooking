<?php

declare(strict_types=1);

namespace Framework\Security\Application\ModifyUserIdentityPassword;

interface ModifyUserIdentityPassword
{
    public function execute(ModifyUserIdentityPasswordCommand $command): void;
}
