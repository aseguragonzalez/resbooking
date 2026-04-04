<?php

declare(strict_types=1);

namespace Framework\Mvc\Security\Application\ModifyUserIdentityPassword;

use Framework\Mvc\Security\Domain\Entities\SignInSession;
use Framework\Mvc\Security\Domain\Exceptions\SessionExpiredException;
use Framework\Mvc\Security\Domain\Exceptions\UserIsNotFoundException;
use Framework\Mvc\Security\Domain\Repositories\SignInSessionRepository;
use Framework\Mvc\Security\Domain\Repositories\UserIdentityRepository;

final readonly class ModifyUserIdentityPasswordHandler implements ModifyUserIdentityPassword
{
    public function __construct(
        private SignInSessionRepository $signInSessionRepository,
        private UserIdentityRepository $userIdentityRepository,
    ) {
    }

    public function execute(ModifyUserIdentityPasswordCommand $command): void
    {
        $session = $this->getSignInSessionOrFail($command->token);
        $user = $this->userIdentityRepository->getByUsername($session->identity->username());
        if ($user === null) {
            throw new UserIsNotFoundException($session->identity->username());
        }
        $user->validatePassword($command->currentPassword);
        $this->userIdentityRepository->save($user->updatePassword($command->newPassword));
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
