<?php

declare(strict_types=1);

namespace Framework\Module\Security\Application\SignUp;

interface SignUp
{
    public function execute(SignUpCommand $command): void;
}
