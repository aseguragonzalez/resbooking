<?php

declare(strict_types=1);

namespace Framework\Module\Security\Application\ActivateUserIdentity;

interface ActivateUserIdentity
{
    public function execute(ActivateUserIdentityCommand $command): void;
}
