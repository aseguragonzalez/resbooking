<?php

declare(strict_types=1);

namespace Framework\Mvc\Security\Application\ResetPasswordFromToken;

use Framework\Mvc\Security\Domain\Exceptions\ResetPasswordChallengeException;
use Framework\Mvc\Security\Domain\Repositories\ResetPasswordChallengeRepository;
use Framework\Mvc\Security\Domain\Repositories\UserIdentityRepository;

final readonly class ResetPasswordFromTokenHandler implements ResetPasswordFromToken
{
    public function __construct(
        private ResetPasswordChallengeRepository $resetPasswordChallengeRepository,
        private UserIdentityRepository $userIdentityRepository,
    ) {
    }

    public function execute(ResetPasswordFromTokenCommand $command): void
    {
        $resetPasswordChallenge = $this->resetPasswordChallengeRepository->getByToken($command->token);
        if ($resetPasswordChallenge === null) {
            return;
        }

        if ($resetPasswordChallenge->isExpired()) {
            $this->resetPasswordChallengeRepository->deleteByToken($command->token);
            throw new ResetPasswordChallengeException($command->token);
        }

        $user = $this->userIdentityRepository->getByUsername($resetPasswordChallenge->userIdentity->username());
        if ($user === null) {
            return;
        }

        $this->userIdentityRepository->save($user->updatePassword($command->newPassword));
    }
}
