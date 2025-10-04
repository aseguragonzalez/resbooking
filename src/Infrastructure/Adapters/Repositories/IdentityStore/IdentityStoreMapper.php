<?php

declare(strict_types=1);

namespace Infrastructure\Adapters\Repositories\IdentityStore;

use Seedwork\Infrastructure\Mvc\Security\Domain\Entities\CurrentIdentity;
use Seedwork\Infrastructure\Mvc\Security\Domain\Entities\ResetPasswordChallenge;
use Seedwork\Infrastructure\Mvc\Security\Domain\Entities\SignInChallenge;
use Seedwork\Infrastructure\Mvc\Security\Domain\Entities\SignInSession;
use Seedwork\Infrastructure\Mvc\Security\Domain\Entities\SignUpChallenge;
use Seedwork\Infrastructure\Mvc\Security\Domain\Entities\UserIdentity;
use Seedwork\Infrastructure\Mvc\Security\Identity;
use Seedwork\Infrastructure\Mvc\Security\Challenge;

final class IdentityStoreMapper
{
    /**
     * @param array<string, mixed> $data
     */
    public function mapToResetPasswordChallenge(array $data): ResetPasswordChallenge
    {
        /** @var string $token */
        $token = $data['token'];
        /** @var string $expiresAt */
        $expiresAt = $data['expiresAt'];
        /** @var array<string, mixed> $userIdentity */
        $userIdentity = $data['userIdentity'];
        return ResetPasswordChallenge::build(
            token: $token,
            expiresAt: new \DateTimeImmutable($expiresAt),
            userIdentity: $this->mapToUserIdentity($userIdentity)
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function mapFromResetPasswordChallenge(ResetPasswordChallenge $resetPasswordChallenge): array
    {
        $timeZone = new \DateTimeZone('UTC');
        return [
            'token' => $resetPasswordChallenge->token,
            'expiresAt' => $resetPasswordChallenge->expiresAt->setTimezone($timeZone)->format('Y-m-d\TH:i:s\Z'),
            'userIdentity' => $this->mapFromUserIdentity($resetPasswordChallenge->userIdentity)
        ];
    }

    /**
     * @param array<string, mixed> $data
     */
    public function mapToSignInChallenge(array $data): SignInChallenge
    {
        /** @var string $token */
        $token = $data['token'];
        /** @var string $expiresAt */
        $expiresAt = $data['expiresAt'];
        return SignInChallenge::build(
            token: $token,
            expiresAt: new \DateTimeImmutable($expiresAt),
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function mapFromSignInChallenge(Challenge $signInChallenge): array
    {
        return [
            'token' => $signInChallenge->getToken(),
            'expiresAt' => $signInChallenge->getExpiresAt()
                ->setTimezone(new \DateTimeZone('UTC'))
                ->format('Y-m-d\TH:i:s\Z'),
        ];
    }

    /**
     * @param array<string, mixed> $data
     */
    public function mapToSignInSession(array $data): SignInSession
    {
        /** @var array<string, mixed> $signInChallenge */
        $signInChallenge = $data['signInChallenge'];
        /** @var array<string, mixed> $identity */
        $identity = $data['identity'];
        return SignInSession::build(
            challenge: $this->mapToSignInChallenge($signInChallenge),
            identity: $this->mapToCurrentIdentity($identity)
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function mapFromSignInSession(SignInSession $signInSession): array
    {
        return [
            'signInChallenge' => $this->mapFromSignInChallenge($signInSession->challenge),
            'identity' => $this->mapFromCurrentIdentity($signInSession->identity)
        ];
    }

    /**
     * @param array<string, mixed> $data
     */
    public function mapToSignUpChallenge(array $data): SignUpChallenge
    {
        /** @var string $token */
        $token = $data['token'];
        /** @var string $expiresAt */
        $expiresAt = $data['expiresAt'];
        /** @var array<string, mixed> $userIdentity */
        $userIdentity = $data['userIdentity'];
        return SignUpChallenge::build(
            token: $token,
            expiresAt: new \DateTimeImmutable($expiresAt),
            userIdentity: $this->mapToUserIdentity($userIdentity)
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function mapFromSignUpChallenge(SignUpChallenge $signUpChallenge): array
    {
        return [
            'token' => $signUpChallenge->token,
            'expiresAt' => $signUpChallenge->expiresAt->setTimezone(new \DateTimeZone('UTC'))->format('Y-m-d\TH:i:s\Z'),
            'userIdentity' => $this->mapFromUserIdentity($signUpChallenge->userIdentity)
        ];
    }

    /**
     * @param array<string, mixed> $data
     */
    public function mapToCurrentIdentity(array $data): CurrentIdentity
    {
        $isAuthenticated = filter_var($data['isAuthenticated'], FILTER_VALIDATE_BOOLEAN);
        /** @var string $roles */
        $roles = $data['roles'];
        /** @var string $username */
        $username = $data['username'];
        return CurrentIdentity::build(
            isAuthenticated: $isAuthenticated,
            roles: explode(',', $roles),
            username: $username
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function mapFromCurrentIdentity(Identity $currentIdentity): array
    {
        return [
            'isAuthenticated' => $currentIdentity->isAuthenticated(),
            'roles' => implode(',', $currentIdentity->getRoles()),
            'username' => $currentIdentity->username()
        ];
    }

    /**
     * @param array<string, mixed> $data
     */
    public function mapToUserIdentity(array $data): UserIdentity
    {
        /** @var string $hash1 */
        $hash1 = $data['hash1'];
        /** @var string $hash2 */
        $hash2 = $data['hash2'];
        /** @var string $roles */
        $roles = $data['roles'];
        /** @var string $seed */
        $seed = $data['seed'];
        /** @var string $username */
        $username = $data['username'];
        return UserIdentity::build(
            isActive: filter_var($data['isActive'], FILTER_VALIDATE_BOOLEAN),
            isBlocked: filter_var($data['isBlocked'], FILTER_VALIDATE_BOOLEAN),
            hash1: $hash1,
            hash2: $hash2,
            roles:  explode(',', $roles),
            seed: $seed,
            username: $username
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function mapFromUserIdentity(UserIdentity $userIdentity): array
    {
        return [
            'isActive' => $userIdentity->isActive,
            'isBlocked' => $userIdentity->isBlocked,
            'hash1' => $userIdentity->hash1,
            'hash2' => $userIdentity->hash2,
            'roles' => implode(',', $userIdentity->roles),
            'seed' => $userIdentity->seed,
            'username' => $userIdentity->username()
        ];
    }
}
