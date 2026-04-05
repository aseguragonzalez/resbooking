<?php

declare(strict_types=1);

namespace Framework\Module\Security\Application\GetIdentity;

use Framework\Module\Security\Identity;

interface GetIdentity
{
    public function execute(GetIdentityCommand $command): Identity;
}
