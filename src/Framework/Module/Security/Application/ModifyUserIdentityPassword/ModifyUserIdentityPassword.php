<?php

declare(strict_types=1);

namespace Framework\Module\Security\Application\ModifyUserIdentityPassword;

interface ModifyUserIdentityPassword
{
    public function execute(ModifyUserIdentityPasswordCommand $command): void;
}
