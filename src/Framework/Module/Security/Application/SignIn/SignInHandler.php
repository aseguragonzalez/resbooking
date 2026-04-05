<?php

declare(strict_types=1);

namespace Framework\Security\Application\SignIn;

use Framework\Security\Challenge;
use Framework\Security\ChallengesExpirationTime;
use Framework\Security\Domain\Entities\SignInSession;
use Framework\Security\Domain\Exceptions\InvalidCredentialsException;
use Framework\Security\Domain\Repositories\SignInSessionRepository;
use Framework\Security\Domain\Repositories\UserIdentityRepository;

final readonly class SignInHandler implements SignIn
{
    public function __construct(
        private UserIdentityRepository $userIdentityRepository,
        private SignInSessionRepository $signInSessionRepository,
        private ChallengesExpirationTime $expirationTime,
    ) {
    }

    public function execute(SignInCommand $command): Challenge
    {
        $user = $this->userIdentityRepository->getByUsername($command->username);
        if ($user === null) {
            throw new InvalidCredentialsException($command->username);
        }

        $authenticatedUser = $user->authenticate($command->password);

        $expiresAt = $command->keepMeSignedIn
            ? $this->expiresAt($this->expirationTime->signInWithRememberMe)
            : $this->expiresAt($this->expirationTime->signIn);

        $session = SignInSession::new($expiresAt, $authenticatedUser);
        $this->signInSessionRepository->save($session);

        return $session->challenge;
    }

    private function expiresAt(int $minutes): \DateTimeImmutable
    {
        return (new \DateTimeImmutable())->modify("+{$minutes} minutes");
    }
}
