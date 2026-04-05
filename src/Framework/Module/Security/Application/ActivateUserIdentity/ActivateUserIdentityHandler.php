<?php

declare(strict_types=1);

namespace Framework\Security\Application\ActivateUserIdentity;

use Framework\Security\Domain\Exceptions\SignUpChallengeException;
use Framework\Security\Domain\Repositories\SignUpChallengeRepository;
use Framework\Security\Domain\Repositories\UserIdentityRepository;

final readonly class ActivateUserIdentityHandler implements ActivateUserIdentity
{
    public function __construct(
        private SignUpChallengeRepository $signUpChallengeRepository,
        private UserIdentityRepository $userIdentityRepository,
    ) {
    }

    public function execute(ActivateUserIdentityCommand $command): void
    {
        $challenge = $this->signUpChallengeRepository->getByToken($command->token);
        if ($challenge === null) {
            throw new SignUpChallengeException($command->token);
        }

        if ($challenge->isExpired()) {
            $this->signUpChallengeRepository->deleteByToken($command->token);
            throw new SignUpChallengeException($command->token);
        }

        $this->userIdentityRepository->save($challenge->userIdentity->activate());
        $this->signUpChallengeRepository->deleteByToken($command->token);
    }
}
