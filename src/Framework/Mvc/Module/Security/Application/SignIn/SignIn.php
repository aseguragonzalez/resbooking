<?php

declare(strict_types=1);

namespace Framework\Mvc\Security\Application\SignIn;

use Framework\Mvc\Security\Challenge;

interface SignIn
{
    public function execute(SignInCommand $command): Challenge;
}
