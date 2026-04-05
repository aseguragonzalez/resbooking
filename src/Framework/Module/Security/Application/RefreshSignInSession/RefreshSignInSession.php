<?php

declare(strict_types=1);

namespace Framework\Module\Security\Application\RefreshSignInSession;

use Framework\Module\Security\Challenge;

interface RefreshSignInSession
{
    public function execute(RefreshSignInSessionCommand $command): Challenge;
}
