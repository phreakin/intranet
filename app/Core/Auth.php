<?php

declare(strict_types=1);

namespace Intranet\Core;

use Intranet\Modules\Shared\Repositories\UserRepository;

final class Auth
{
    public static function user(): ?array
    {
        if (empty($_SESSION['uid'])) {
            return null;
        }

        return (new UserRepository())->findById((int) $_SESSION['uid']);
    }

    public static function login(array $user): void
    {
        session_regenerate_id(true);
        $_SESSION['uid'] = (int) $user['id'];
    }

    public static function logout(): void
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
        }
        session_destroy();
    }

    public static function check(): bool
    {
        return self::user() !== null;
    }

    public static function requireAuth(): void
    {
        if (!self::check()) {
            Helpers::redirect('/login');
        }
    }

    public static function hasRole(string ...$roles): bool
    {
        $user = self::user();
        if ($user === null) {
            return false;
        }

        $userRoles = array_map('strtolower', array_filter(explode(',', (string) ($user['roles'] ?? ''))));
        foreach ($roles as $role) {
            if (in_array(strtolower($role), $userRoles, true)) {
                return true;
            }
        }

        return false;
    }

    public static function requireRole(string ...$roles): void
    {
        if (!self::hasRole(...$roles)) {
            http_response_code(403);
            exit('Forbidden');
        }
    }

    public static function hasPermission(string ...$permissions): bool
    {
        $user = self::user();
        if ($user === null) {
            return false;
        }

        $userPermissions = array_map('strtolower', array_filter(explode(',', (string) ($user['permissions'] ?? ''))));
        foreach ($permissions as $permission) {
            if (in_array(strtolower($permission), $userPermissions, true)) {
                return true;
            }
        }

        return false;
    }

    public static function requirePermission(string ...$permissions): void
    {
        if (!self::hasPermission(...$permissions)) {
            http_response_code(403);
            exit('Forbidden');
        }
    }
}
