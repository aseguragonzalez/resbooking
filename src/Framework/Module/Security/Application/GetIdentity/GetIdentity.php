<?php

declare(strict_types=1);

namespace Framework\Security\Application\GetIdentity;

use Framework\Security\Identity;

interface GetIdentity
{
    public function execute(GetIdentityCommand $command): Identity;
}
