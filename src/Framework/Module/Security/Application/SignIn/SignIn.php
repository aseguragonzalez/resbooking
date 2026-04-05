<?php

declare(strict_types=1);

namespace Framework\Module\Security\Application\SignIn;

use Framework\Module\Security\Challenge;

interface SignIn
{
    public function execute(SignInCommand $command): Challenge;
}
