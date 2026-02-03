<?php

declare(strict_types=1);

namespace Framework\Mvc\Security\Infrastructure;

use Framework\Mvc\Security\Domain\Entities\ResetPasswordChallenge;
use Framework\Mvc\Security\Domain\Entities\UserIdentity;
use Framework\Mvc\Security\Domain\Repositories\ResetPasswordChallengeRepository;
use PDO;

final readonly class SqlResetPasswordChallengeRepository implements ResetPasswordChallengeRepository
{
    public function __construct(private PDO $db)
    {
    }

    public function save(ResetPasswordChallenge $challenge): void
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

    public function getByToken(string $token): ?ResetPasswordChallenge
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

        $roles = $this->fetchRolesForUser($row['user_id']);

        $userIdentity = UserIdentity::build(
            hash1: $row['hash1'],
            hash2: $row['hash2'],
            roles: $roles,
            seed: $row['seed'],
            username: $row['user_id'],
            isActive: (bool) $row['is_active'],
            isBlocked: (bool) $row['is_blocked']
        );

        return ResetPasswordChallenge::build(
            token: $row['id'],
            expiresAt: new \DateTimeImmutable($row['expires_at']),
            userIdentity: $userIdentity
        );
    }

    public function deleteByToken(string $token): void
    {
        $sql = 'DELETE FROM reset_password_challenges WHERE id = :token';
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['token' => $token]);
    }

    /**
     * @return array<string>
     */
    private function fetchRolesForUser(string $userId): array
    {
        $rolesSql = 'SELECT role FROM user_roles WHERE user_id = :user_id';
        $rolesStmt = $this->db->prepare($rolesSql);
        $rolesStmt->execute(['user_id' => $userId]);

        /** @var array<int, array{role: string}> $rolesData */
        $rolesData = $rolesStmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(fn ($r) => $r['role'], $rolesData);
    }
}
