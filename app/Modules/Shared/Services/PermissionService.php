<?php

declare(strict_types=1);

namespace Intranet\Modules\Shared\Services;

use Intranet\Core\Database;

/**
 * Resolves permissions through role_permissions.
 * Caches the per-user permission set for the lifetime of a request.
 */
final class PermissionService
{
    /** @var array<int,array<string,bool>> */
    private static array $cache = [];

    /**
     * @return array<string>
     */
    public function permissionsForUser(int $userId): array
    {
        if (isset(self::$cache[$userId])) {
            return array_keys(self::$cache[$userId]);
        }

        $sql = 'SELECT DISTINCT p.name
                  FROM permissions p
                  JOIN role_permissions rp ON rp.permission_id = p.id
                  JOIN user_roles ur ON ur.role_id = rp.role_id
                 WHERE ur.user_id = :uid';
        $stmt = Database::connection()->prepare($sql);
        $stmt->execute(['uid' => $userId]);

        $set = [];
        foreach ($stmt->fetchAll() as $row) {
            $set[(string) $row['name']] = true;
        }
        self::$cache[$userId] = $set;

        return array_keys($set);
    }

    public function userCan(int $userId, string $permission): bool
    {
        if (!isset(self::$cache[$userId])) {
            $this->permissionsForUser($userId);
        }
        return isset(self::$cache[$userId][$permission]);
    }

    public static function flushCache(?int $userId = null): void
    {
        if ($userId === null) {
            self::$cache = [];
            return;
        }
        unset(self::$cache[$userId]);
    }

    /**
     * @return array<int,array{id:int,name:string}>
     */
    public function allPermissions(): array
    {
        return Database::connection()
            ->query('SELECT id, name FROM permissions ORDER BY name ASC')
            ->fetchAll();
    }

    /**
     * @return array<int,array{id:int,name:string}>
     */
    public function allRoles(): array
    {
        return Database::connection()
            ->query('SELECT id, name FROM roles ORDER BY name ASC')
            ->fetchAll();
    }

    /**
     * @return array<int,array<int,true>> role_id => [permission_id => true]
     */
    public function rolePermissionMatrix(): array
    {
        $rows = Database::connection()
            ->query('SELECT role_id, permission_id FROM role_permissions')
            ->fetchAll();

        $matrix = [];
        foreach ($rows as $row) {
            $matrix[(int) $row['role_id']][(int) $row['permission_id']] = true;
        }
        return $matrix;
    }

    /**
     * Replace role's permission grants with the given list of permission IDs.
     *
     * @param array<int> $permissionIds
     */
    public function setRolePermissions(int $roleId, array $permissionIds): void
    {
        $db = Database::connection();
        $db->beginTransaction();
        try {
            $db->prepare('DELETE FROM role_permissions WHERE role_id = :role_id')
                ->execute(['role_id' => $roleId]);

            $insert = $db->prepare('INSERT INTO role_permissions (role_id, permission_id) VALUES (:role_id, :permission_id)');
            foreach (array_unique(array_map('intval', $permissionIds)) as $pid) {
                if ($pid <= 0) {
                    continue;
                }
                $insert->execute(['role_id' => $roleId, 'permission_id' => $pid]);
            }
            $db->commit();
        } catch (\Throwable $e) {
            $db->rollBack();
            throw $e;
        }

        self::flushCache();
    }
}
