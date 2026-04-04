<?php

declare(strict_types=1);

namespace Framework\Mvc\Security\Application\RefreshSignInSession;

use Framework\Mvc\Security\Challenge;

interface RefreshSignInSession
{
    public function execute(RefreshSignInSessionCommand $command): Challenge;
}
