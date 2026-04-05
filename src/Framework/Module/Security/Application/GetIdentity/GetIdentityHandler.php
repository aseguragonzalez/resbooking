<?php

declare(strict_types=1);

namespace Framework\Security\Application\GetIdentity;

use Framework\Security\Domain\Entities\SignInSession;
use Framework\Security\Domain\Entities\UserIdentity;
use Framework\Security\Domain\Exceptions\SessionExpiredException;
use Framework\Security\Domain\Repositories\SignInSessionRepository;
use Framework\Security\Identity;

final readonly class GetIdentityHandler implements GetIdentity
{
    public function __construct(
        private SignInSessionRepository $signInSessionRepository,
    ) {
    }

    public function execute(GetIdentityCommand $command): Identity
    {
        if (!isset($command->token) || empty($command->token) || empty(trim($command->token))) {
            return UserIdentity::anonymous();
        }

        $session = $this->getSignInSessionOrFail($command->token);

        return $session->identity;
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
}
