<?php

declare(strict_types=1);

namespace Infrastructure\Adapters\Repositories\IdentityStore;

use Framework\Mvc\Security\IdentityStore;
use Framework\Mvc\Security\Domain\Entities\ResetPasswordChallenge;
use Framework\Mvc\Security\Domain\Entities\SignInChallenge;
use Framework\Mvc\Security\Domain\Entities\SignInSession;
use Framework\Mvc\Security\Domain\Entities\SignUpChallenge;
use Framework\Mvc\Security\Domain\Entities\UserIdentity;

final class InFileIdentityStore implements IdentityStore
{
    /**
     * @var array<string, ResetPasswordChallenge>
     */
    private array $resetPasswordChallenges = [];

    /**
     * @var array<string, SignInChallenge>
     */
    private array $signInChallenges = [];

    /**
     * @var array<string, SignInSession>
     */
    private array $signInSessions = [];

    /**
     * @var array<string, SignUpChallenge>
     */
    private array $signUpChallenges = [];

    /**
     * @var array<string, UserIdentity>
     */
    private array $userIdentities = [];

    public function __construct(
        private readonly IdentityStoreMapper $identityStoreMapper,
        private readonly string $dataFilePath = __DIR__ . '/inmemory_identity_manager.json',
    ) {
        $file = $this->dataFilePath;
        if (!file_exists($file)) {
            return;
        }

        $fileContent = file_get_contents($file);
        if ($fileContent === false) {
            return;
        }

        $data = json_decode($fileContent, true);
        if (!is_array($data)) {
            return;
        }

        if (isset($data['resetPasswordChallenges']) && is_array($data['resetPasswordChallenges'])) {
            /** @var array<string, mixed> $challengeData */
            foreach ($data['resetPasswordChallenges'] as $challengeData) {
                $challenge = $this->identityStoreMapper->mapToResetPasswordChallenge($challengeData);
                $this->resetPasswordChallenges[$challenge->getToken()] = $challenge;
            }
        }

        if (isset($data['signInChallenges']) && is_array($data['signInChallenges'])) {
            /** @var array<string, mixed> $challengeData */
            foreach ($data['signInChallenges'] as $challengeData) {
                $challenge = $this->identityStoreMapper->mapToSignInChallenge($challengeData);
                $this->signInChallenges[$challenge->getToken()] = $challenge;
            }
        }

        if (isset($data['signInSessions']) && is_array($data['signInSessions'])) {
            /** @var array<string, mixed> $sessionData */
            foreach ($data['signInSessions'] as $sessionData) {
                $session = $this->identityStoreMapper->mapToSignInSession($sessionData);
                $this->signInSessions[$session->challenge->getToken()] = $session;
            }
        }

        if (isset($data['signUpChallenges']) && is_array($data['signUpChallenges'])) {
            /** @var array<string, mixed> $challengeData */
            foreach ($data['signUpChallenges'] as $challengeData) {
                $challenge = $this->identityStoreMapper->mapToSignUpChallenge($challengeData);
                $this->signUpChallenges[$challenge->getToken()] = $challenge;
            }
        }

        if (isset($data['userIdentities']) && is_array($data['userIdentities'])) {
            /** @var array<string, mixed> $userData */
            foreach ($data['userIdentities'] as $userData) {
                $userIdentity = $this->identityStoreMapper->mapToUserIdentity($userData);
                $this->userIdentities[$userIdentity->username()] = $userIdentity;
            }
        }
    }

    public function __destruct()
    {
        $data = [
            'resetPasswordChallenges' => array_map(
                fn ($challenge) => $this->identityStoreMapper->mapFromResetPasswordChallenge($challenge),
                $this->resetPasswordChallenges
            ),
            'signInChallenges' => array_map(
                fn ($challenge) => $this->identityStoreMapper->mapFromSignInChallenge($challenge),
                $this->signInChallenges
            ),
            'signInSessions' => array_map(
                fn ($session) => $this->identityStoreMapper->mapFromSignInSession($session),
                $this->signInSessions
            ),
            'signUpChallenges' => array_map(
                fn ($challenge) => $this->identityStoreMapper->mapFromSignUpChallenge($challenge),
                $this->signUpChallenges
            ),
            'userIdentities' => array_map(
                fn ($user) => $this->identityStoreMapper->mapFromUserIdentity($user),
                $this->userIdentities
            ),
        ];

        file_put_contents($this->dataFilePath, json_encode($data, JSON_PRETTY_PRINT));
    }

    public function deleteSignInSessionByToken(string $token): void
    {
        unset($this->signInSessions[$token]);
    }

    public function deleteResetPasswordChallengeByToken(string $token): void
    {
        unset($this->resetPasswordChallenges[$token]);
    }

    public function deleteSignUpChallengeByToken(string $token): void
    {
        unset($this->signUpChallenges[$token]);
    }

    public function existsUserIdentityByUsername(string $username): bool
    {
        return isset($this->userIdentities[$username]);
    }

    public function getResetPasswordChallengeByToken(string $token): ?ResetPasswordChallenge
    {
        return $this->resetPasswordChallenges[$token] ?? null;
    }

    public function getSignInSessionByToken(string $token): ?SignInSession
    {
        return $this->signInSessions[$token] ?? null;
    }

    public function getSignUpChallengeByToken(string $token): ?SignUpChallenge
    {
        return $this->signUpChallenges[$token] ?? null;
    }

    public function getUserIdentityByUsername(string $username): ?UserIdentity
    {
        return $this->userIdentities[$username] ?? null;
    }

    public function saveResetPasswordChallenge(ResetPasswordChallenge $challenge): void
    {
        $this->resetPasswordChallenges[$challenge->getToken()] = $challenge;
    }

    public function saveSignInSession(SignInSession $session): void
    {
        $this->signInSessions[$session->challenge->getToken()] = $session;
    }

    public function saveSignUpChallenge(SignUpChallenge $challenge): void
    {
        $this->signUpChallenges[$challenge->getToken()] = $challenge;
    }

    public function saveUserIdentity(UserIdentity $user): void
    {
        $this->userIdentities[$user->username()] = $user;
    }
}
