<?php

declare(strict_types=1);

namespace Framework\Mvc\Security\Application\GetIdentity;

use Framework\Mvc\Security\Identity;

interface GetIdentity
{
    public function execute(GetIdentityCommand $command): Identity;
}
