<?php

declare(strict_types=1);

namespace Intranet\Modules\Admin\Controllers;

use Intranet\Core\Auth;
use Intranet\Core\Csrf;
use Intranet\Core\Helpers;
use Intranet\Core\View;
use Intranet\Modules\Shared\Services\PermissionService;

final class AdminRbacController
{
    public function index(): void
    {
        Auth::requireAuth();
        Auth::requireRole('Admin');

        $svc = new PermissionService();
        View::render('admin/rbac', [
            'title' => 'Role & Permission Matrix',
            'roles' => $svc->allRoles(),
            'permissions' => $svc->allPermissions(),
            'matrix' => $svc->rolePermissionMatrix(),
            'csrf' => Csrf::token(),
        ]);
    }

    public function updateRole(int $roleId): void
    {
        Auth::requireAuth();
        Auth::requireRole('Admin');
        if (!Csrf::check($_POST['_csrf'] ?? null)) {
            http_response_code(422);
            exit('Invalid CSRF token.');
        }

        $permissionIds = array_map('intval', (array) ($_POST['permission_ids'] ?? []));
        (new PermissionService())->setRolePermissions($roleId, $permissionIds);

        Helpers::redirect('/admin/rbac');
    }
}
