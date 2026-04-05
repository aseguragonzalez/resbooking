<?php

declare(strict_types=1);

namespace Framework\Security\Application\SignIn;

use Framework\Security\Challenge;

interface SignIn
{
    public function execute(SignInCommand $command): Challenge;
}
