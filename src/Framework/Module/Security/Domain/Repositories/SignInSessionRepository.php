<?php

declare(strict_types=1);

namespace Framework\Security\Domain\Repositories;

use Framework\Security\Domain\Entities\SignInSession;

interface SignInSessionRepository
{
    public function save(SignInSession $session): void;

    public function getByToken(string $token): ?SignInSession;

    public function deleteByToken(string $token): void;
}
