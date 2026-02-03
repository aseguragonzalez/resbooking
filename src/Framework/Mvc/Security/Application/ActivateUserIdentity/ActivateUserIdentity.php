<?php

declare(strict_types=1);

namespace Framework\Mvc\Security\Application\ActivateUserIdentity;

interface ActivateUserIdentity
{
    public function execute(ActivateUserIdentityCommand $command): void;
}
