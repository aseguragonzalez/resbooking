<?php

declare(strict_types=1);

namespace Framework\Security\Application\RefreshSignInSession;

use Framework\Security\Challenge;
use Framework\Security\ChallengesExpirationTime;
use Framework\Security\Domain\Entities\SignInSession;
use Framework\Security\Domain\Exceptions\SessionExpiredException;
use Framework\Security\Domain\Repositories\SignInSessionRepository;

final readonly class RefreshSignInSessionHandler implements RefreshSignInSession
{
    public function __construct(
        private SignInSessionRepository $signInSessionRepository,
        private ChallengesExpirationTime $expirationTime,
    ) {
    }

    public function execute(RefreshSignInSessionCommand $command): Challenge
    {
        $session = $this->getSignInSessionOrFail($command->token);
        $sessionUpdated = $session->refreshUntil($this->expiresAt($this->expirationTime->refresh));
        $this->signInSessionRepository->save($sessionUpdated);

        return $sessionUpdated->challenge;
    }

    private function getSignInSessionOrFail(string $token): SignInSession
    {
        $session = $this->signInSessionRepository->getByToken($token);
        if ($session === null) {
            throw new SessionExpiredException();
        }

        if ($session->isExpired()) {
            $this->signInSessionRepository->deleteByToken($token);
            throw new SessionExpiredException();
        }

        return $session;
    }

    private function expiresAt(int $minutes): \DateTimeImmutable
    {
        return (new \DateTimeImmutable())->modify("+{$minutes} minutes");
    }
}
