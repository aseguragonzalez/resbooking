<?php

declare(strict_types=1);

namespace Infrastructure\Adapters\Repositories\IdentityStore;

use Framework\Mvc\Security\IdentityStore;
use Framework\Mvc\Security\Domain\Entities\CurrentIdentity;
use Framework\Mvc\Security\Domain\Entities\ResetPasswordChallenge;
use Framework\Mvc\Security\Domain\Entities\SignInChallenge;
use Framework\Mvc\Security\Domain\Entities\SignInSession;
use Framework\Mvc\Security\Domain\Entities\SignUpChallenge;
use Framework\Mvc\Security\Domain\Entities\UserIdentity;
use PDO;

final class SqlIdentityStore implements IdentityStore
{
    public function __construct(
        private readonly PDO $db,
    ) {
    }

    public function saveUserIdentity(UserIdentity $user): void
    {
        $username = $user->username();

        $sql = <<<SQL
            INSERT INTO users (
                id,
                hash1,
                hash2,
                is_active,
                is_blocked,
                seed
            )
            VALUES (
                :id,
                :hash1,
                :hash2,
                :is_active,
                :is_blocked,
                :seed
            )
            ON DUPLICATE KEY UPDATE
                hash1 = VALUES(hash1),
                hash2 = VALUES(hash2),
                is_active = VALUES(is_active),
                is_blocked = VALUES(is_blocked),
                seed = VALUES(seed)
        SQL;

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'id' => $username,
            'hash1' => $user->hash1,
            'hash2' => $user->hash2,
            'is_active' => $user->isActive ? 1 : 0,
            'is_blocked' => $user->isBlocked ? 1 : 0,
            'seed' => $user->seed,
        ]);

        // Delete existing roles
        $deleteRolesSql = 'DELETE FROM user_roles WHERE user_id = :user_id';
        $deleteStmt = $this->db->prepare($deleteRolesSql);
        $deleteStmt->execute(['user_id' => $username]);

        // Insert new roles
        if (!empty($user->roles)) {
            $insertRoleSql = 'INSERT INTO user_roles (user_id, role) VALUES (:user_id, :role)';
            $insertRoleStmt = $this->db->prepare($insertRoleSql);
            foreach ($user->roles as $role) {
                $insertRoleStmt->execute([
                    'user_id' => $username,
                    'role' => $role,
                ]);
            }
        }
    }

    public function getUserIdentityByUsername(string $username): ?UserIdentity
    {
        $sql = 'SELECT * FROM users WHERE id = :username';
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['username' => $username]);

        /** @var array{id: string, hash1: string, hash2: string, is_active: int|bool, is_blocked: int|bool, seed: string}|false $userData */
        $userData = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($userData === false) {
            return null;
        }

        // Fetch roles
        $rolesSql = 'SELECT role FROM user_roles WHERE user_id = :user_id';
        $rolesStmt = $this->db->prepare($rolesSql);
        $rolesStmt->execute(['user_id' => $username]);

        /** @var array<int, array{role: string}> $rolesData */
        $rolesData = $rolesStmt->fetchAll(PDO::FETCH_ASSOC);
        $roles = array_map(fn ($row) => $row['role'], $rolesData);

        return UserIdentity::build(
            hash1: $userData['hash1'],
            hash2: $userData['hash2'],
            roles: $roles,
            seed: $userData['seed'],
            username: $userData['id'],
            isActive: (bool) $userData['is_active'],
            isBlocked: (bool) $userData['is_blocked']
        );
    }

    public function existsUserIdentityByUsername(string $username): bool
    {
        $sql = 'SELECT COUNT(*) as count FROM users WHERE id = :username';
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['username' => $username]);

        /** @var array{count: string}|false $result */
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result === false) {
            return false;
        }

        return (int) $result['count'] > 0;
    }

    public function saveSignInSession(SignInSession $session): void
    {
        $token = $session->challenge->getToken();
        $expiresAt = $session->challenge->getExpiresAt();
        $username = $session->identity->username();

        // Save challenge
        $challengeSql = <<<SQL
            INSERT INTO sign_in_challenges (id, expires_at)
            VALUES (:id, :expires_at)
            ON DUPLICATE KEY UPDATE
                expires_at = VALUES(expires_at)
        SQL;

        $challengeStmt = $this->db->prepare($challengeSql);
        $challengeStmt->execute([
            'id' => $token,
            'expires_at' => $expiresAt->format('Y-m-d H:i:s'),
        ]);

        // Save session
        $sessionSql = <<<SQL
            INSERT INTO sign_in_sessions (sign_in_challenge_id, user_id)
            VALUES (:sign_in_challenge_id, :user_id)
            ON DUPLICATE KEY UPDATE
                sign_in_challenge_id = VALUES(sign_in_challenge_id),
                user_id = VALUES(user_id)
        SQL;

        $sessionStmt = $this->db->prepare($sessionSql);
        $sessionStmt->execute([
            'sign_in_challenge_id' => $token,
            'user_id' => $username,
        ]);
    }

    public function getSignInSessionByToken(string $token): ?SignInSession
    {
        $sql = <<<SQL
            SELECT
                sic.id as challenge_id,
                sic.expires_at as challenge_expires_at,
                u.id as user_id
            FROM sign_in_sessions sis
            INNER JOIN sign_in_challenges sic ON sis.sign_in_challenge_id = sic.id
            INNER JOIN users u ON sis.user_id = u.id
            WHERE sic.id = :token
        SQL;

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['token' => $token]);

        /** @var array{challenge_id: string, challenge_expires_at: string, user_id: string}|false $row */
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row === false) {
            return null;
        }

        // Fetch roles
        $rolesSql = 'SELECT role FROM user_roles WHERE user_id = :user_id';
        $rolesStmt = $this->db->prepare($rolesSql);
        $rolesStmt->execute(['user_id' => $row['user_id']]);

        /** @var array<int, array{role: string}> $rolesData */
        $rolesData = $rolesStmt->fetchAll(PDO::FETCH_ASSOC);
        $roles = array_map(fn ($r) => $r['role'], $rolesData);

        // Build challenge
        $challenge = SignInChallenge::build(
            token: $row['challenge_id'],
            expiresAt: new \DateTimeImmutable($row['challenge_expires_at'])
        );

        // Build identity
        $identity = CurrentIdentity::build(
            isAuthenticated: true,
            roles: $roles,
            username: $row['user_id']
        );

        // Build session
        return SignInSession::build(
            challenge: $challenge,
            identity: $identity
        );
    }

    public function deleteSignInSessionByToken(string $token): void
    {
        $sql = 'DELETE FROM sign_in_sessions WHERE sign_in_challenge_id = :token';
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['token' => $token]);
    }

    public function saveSignUpChallenge(SignUpChallenge $challenge): void
    {
        $token = $challenge->token;
        $expiresAt = $challenge->expiresAt;
        $username = $challenge->userIdentity->username();

        $sql = <<<SQL
            INSERT INTO sign_up_challenges (id, expires_at, user_id)
            VALUES (:id, :expires_at, :user_id)
            ON DUPLICATE KEY UPDATE
                expires_at = VALUES(expires_at),
                user_id = VALUES(user_id)
        SQL;

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'id' => $token,
            'expires_at' => $expiresAt->format('Y-m-d H:i:s'),
            'user_id' => $username,
        ]);
    }

    public function getSignUpChallengeByToken(string $token): ?SignUpChallenge
    {
        $sql = <<<SQL
            SELECT
                suc.id,
                suc.expires_at,
                suc.user_id,
                u.hash1,
                u.hash2,
                u.is_active,
                u.is_blocked,
                u.seed
            FROM sign_up_challenges suc
            INNER JOIN users u ON suc.user_id = u.id
            WHERE suc.id = :token
        SQL;

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['token' => $token]);

        /** @var array{id: string, expires_at: string, user_id: string, hash1: string, hash2: string, is_active: int|bool, is_blocked: int|bool, seed: string}|false $row */
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row === false) {
            return null;
        }

        // Fetch roles
        $rolesSql = 'SELECT role FROM user_roles WHERE user_id = :user_id';
        $rolesStmt = $this->db->prepare($rolesSql);
        $rolesStmt->execute(['user_id' => $row['user_id']]);

        /** @var array<int, array{role: string}> $rolesData */
        $rolesData = $rolesStmt->fetchAll(PDO::FETCH_ASSOC);
        $roles = array_map(fn ($r) => $r['role'], $rolesData);

        // Build UserIdentity
        $userIdentity = UserIdentity::build(
            hash1: $row['hash1'],
            hash2: $row['hash2'],
            roles: $roles,
            seed: $row['seed'],
            username: $row['user_id'],
            isActive: (bool) $row['is_active'],
            isBlocked: (bool) $row['is_blocked']
        );

        // Build SignUpChallenge
        return SignUpChallenge::build(
            token: $row['id'],
            expiresAt: new \DateTimeImmutable($row['expires_at']),
            userIdentity: $userIdentity
        );
    }

    public function deleteSignUpChallengeByToken(string $token): void
    {
        $sql = 'DELETE FROM sign_up_challenges WHERE id = :token';
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['token' => $token]);
    }

    public function saveResetPasswordChallenge(ResetPasswordChallenge $challenge): void
    {
        $token = $challenge->token;
        $expiresAt = $challenge->expiresAt;
        $username = $challenge->userIdentity->username();

        $sql = <<<SQL
            INSERT INTO reset_password_challenges (id, expires_at, user_id)
            VALUES (:id, :expires_at, :user_id)
            ON DUPLICATE KEY UPDATE
                expires_at = VALUES(expires_at),
                user_id = VALUES(user_id)
        SQL;

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'id' => $token,
            'expires_at' => $expiresAt->format('Y-m-d H:i:s'),
            'user_id' => $username,
        ]);
    }

    public function getResetPasswordChallengeByToken(string $token): ?ResetPasswordChallenge
    {
        $sql = <<<SQL
            SELECT
                rpc.id,
                rpc.expires_at,
                rpc.user_id,
                u.hash1,
                u.hash2,
                u.is_active,
                u.is_blocked,
                u.seed
            FROM reset_password_challenges rpc
            INNER JOIN users u ON rpc.user_id = u.id
            WHERE rpc.id = :token
        SQL;

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['token' => $token]);

        /** @var array{id: string, expires_at: string, user_id: string, hash1: string, hash2: string, is_active: int|bool, is_blocked: int|bool, seed: string}|false $row */
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row === false) {
            return null;
        }

        // Fetch roles
        $rolesSql = 'SELECT role FROM user_roles WHERE user_id = :user_id';
        $rolesStmt = $this->db->prepare($rolesSql);
        $rolesStmt->execute(['user_id' => $row['user_id']]);

        /** @var array<int, array{role: string}> $rolesData */
        $rolesData = $rolesStmt->fetchAll(PDO::FETCH_ASSOC);
        $roles = array_map(fn ($r) => $r['role'], $rolesData);

        // Build UserIdentity
        $userIdentity = UserIdentity::build(
            hash1: $row['hash1'],
            hash2: $row['hash2'],
            roles: $roles,
            seed: $row['seed'],
            username: $row['user_id'],
            isActive: (bool) $row['is_active'],
            isBlocked: (bool) $row['is_blocked']
        );

        // Build ResetPasswordChallenge
        return ResetPasswordChallenge::build(
            token: $row['id'],
            expiresAt: new \DateTimeImmutable($row['expires_at']),
            userIdentity: $userIdentity
        );
    }

    public function deleteResetPasswordChallengeByToken(string $token): void
    {
        $sql = 'DELETE FROM reset_password_challenges WHERE id = :token';
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['token' => $token]);
    }
}
