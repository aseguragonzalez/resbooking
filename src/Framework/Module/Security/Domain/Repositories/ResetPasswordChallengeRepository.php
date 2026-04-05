<?php

declare(strict_types=1);

namespace Framework\Security\Domain\Repositories;

use Framework\Security\Domain\Entities\ResetPasswordChallenge;

interface ResetPasswordChallengeRepository
{
    public function save(ResetPasswordChallenge $challenge): void;

    public function getByToken(string $token): ?ResetPasswordChallenge;

    public function deleteByToken(string $token): void;
}
