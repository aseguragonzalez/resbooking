<?php

declare(strict_types=1);

namespace Framework\Security\Application\RefreshSignInSession;

use Framework\Security\Challenge;

interface RefreshSignInSession
{
    public function execute(RefreshSignInSessionCommand $command): Challenge;
}
