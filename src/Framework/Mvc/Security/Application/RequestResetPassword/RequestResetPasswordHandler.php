<?php

declare(strict_types=1);

namespace Framework\Mvc\Security\Application\RequestResetPassword;

use Framework\Mvc\Security\ChallengeNotificator;
use Framework\Mvc\Security\ChallengesExpirationTime;
use Framework\Mvc\Security\Domain\Entities\ResetPasswordChallenge;
use Framework\Mvc\Security\Domain\Repositories\ResetPasswordChallengeRepository;
use Framework\Mvc\Security\Domain\Repositories\UserIdentityRepository;

final readonly class RequestResetPasswordHandler implements RequestResetPassword
{
    public function __construct(
        private UserIdentityRepository $userIdentityRepository,
        private ResetPasswordChallengeRepository $resetPasswordChallengeRepository,
        private ChallengeNotificator $notificator,
        private ChallengesExpirationTime $expirationTime,
    ) {
    }

    public function execute(RequestResetPasswordCommand $command): void
    {
        $user = $this->userIdentityRepository->getByUsername($command->username);
        if ($user === null) {
            return;
        }

        $challenge = ResetPasswordChallenge::new(
            $this->expiresAt($this->expirationTime->resetPasswordChallenge),
            $user
        );
        $this->resetPasswordChallengeRepository->save($challenge);
        $this->notificator->sendResetPasswordChallenge($command->username, $challenge);
    }

    private function expiresAt(int $minutes): \DateTimeImmutable
    {
        return (new \DateTimeImmutable())->modify("+{$minutes} minutes");
    }
}
