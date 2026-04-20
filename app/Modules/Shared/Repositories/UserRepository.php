<?php

declare(strict_types=1);

namespace Intranet\Modules\Shared\Repositories;

use Intranet\Core\Database;
use PDO;

final class UserRepository
{
    public function findById(int $id): ?array
    {
        $sql = 'SELECT u.*, GROUP_CONCAT(r.name) AS roles
                FROM users u
                LEFT JOIN user_roles ur ON ur.user_id = u.id
                LEFT JOIN roles r ON r.id = ur.role_id
                WHERE u.id = :id GROUP BY u.id';
        $stmt = Database::connection()->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public function findByEmail(string $email): ?array
    {
        $stmt = Database::connection()->prepare('SELECT * FROM users WHERE email = :email LIMIT 1');
        $stmt->execute(['email' => $email]);
        return $stmt->fetch() ?: null;
    }

    public function createLocal(string $email, string $displayName, string $password): int
    {
        $sql = 'INSERT INTO users (email, display_name, password_hash, created_at, updated_at) VALUES (:email, :display_name, :password_hash, NOW(), NOW())';
        $stmt = Database::connection()->prepare($sql);
        $stmt->execute([
            'email' => $email,
            'display_name' => $displayName,
            'password_hash' => password_hash($password, PASSWORD_DEFAULT),
        ]);

        $id = (int) Database::connection()->lastInsertId();
        Database::connection()->prepare('INSERT INTO user_roles (user_id, role_id) SELECT :uid, id FROM roles WHERE name = :role')
            ->execute(['uid' => $id, 'role' => 'Member']);

        return $id;
    }

    public function attachOAuth(int $userId, string $provider, string $providerId): void
    {
        $sql = 'INSERT INTO oauth_accounts (user_id, provider, provider_user_id, created_at)
                VALUES (:user_id, :provider, :provider_user_id, NOW())
                ON DUPLICATE KEY UPDATE user_id = VALUES(user_id)';
        Database::connection()->prepare($sql)->execute([
            'user_id' => $userId,
            'provider' => $provider,
            'provider_user_id' => $providerId,
        ]);
    }

    public function findByOAuth(string $provider, string $providerId): ?array
    {
        $sql = 'SELECT u.* FROM users u JOIN oauth_accounts oa ON oa.user_id = u.id
                WHERE oa.provider = :provider AND oa.provider_user_id = :provider_user_id LIMIT 1';
        $stmt = Database::connection()->prepare($sql);
        $stmt->execute(['provider' => $provider, 'provider_user_id' => $providerId]);
        return $stmt->fetch() ?: null;
    }

    public function profileWithStats(int $id): ?array
    {
        $sql = 'SELECT u.*, GROUP_CONCAT(DISTINCT r.name) AS roles,
                       COUNT(DISTINCT p.id) AS post_count,
                       COUNT(DISTINCT c.id) AS comment_count,
                       COUNT(DISTINCT uf.id) AS favorite_count,
                       COUNT(DISTINCT ub.id) AS bookmark_count,
                       GROUP_CONCAT(DISTINCT b.name) AS badges
                FROM users u
                LEFT JOIN user_roles ur ON ur.user_id = u.id
                LEFT JOIN roles r ON r.id = ur.role_id
                LEFT JOIN posts p ON p.user_id = u.id
                LEFT JOIN comments c ON c.user_id = u.id
                LEFT JOIN post_favorites uf ON uf.user_id = u.id
                LEFT JOIN post_bookmarks ub ON ub.user_id = u.id
                LEFT JOIN user_badges ubd ON ubd.user_id = u.id
                LEFT JOIN badges b ON b.id = ubd.badge_id
                WHERE u.id = :id
                GROUP BY u.id';
        $stmt = Database::connection()->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }
}
