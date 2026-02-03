<?php

declare(strict_types=1);

namespace Framework\Mvc\Security\Infrastructure;

use Framework\Mvc\Security\Domain\Entities\UserIdentity;
use Framework\Mvc\Security\Domain\Repositories\UserIdentityRepository;
use PDO;

final class SqlUserIdentityRepository implements UserIdentityRepository
{
    public function __construct(
        private readonly PDO $db,
    ) {
    }

    public function save(UserIdentity $user): void
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

        $deleteRolesSql = 'DELETE FROM user_roles WHERE user_id = :user_id';
        $deleteStmt = $this->db->prepare($deleteRolesSql);
        $deleteStmt->execute(['user_id' => $username]);

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

    public function getByUsername(string $username): ?UserIdentity
    {
        $sql = 'SELECT * FROM users WHERE id = :username';
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['username' => $username]);

        /** @var array{id: string, hash1: string, hash2: string, is_active: int|bool, is_blocked: int|bool, seed: string}|false $userData */
        $userData = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($userData === false) {
            return null;
        }

        $roles = $this->fetchRolesForUser($username);

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

    public function existsByUsername(string $username): bool
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

        return array_map(fn ($row) => $row['role'], $rolesData);
    }
}
