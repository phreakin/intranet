<?php

declare(strict_types=1);

namespace Intranet\Modules\Admin\Controllers;

use Intranet\Core\Auth;
use Intranet\Core\Csrf;
use Intranet\Core\Helpers;
use Intranet\Core\View;
use Intranet\Modules\Shared\Repositories\AdminRepository;

final class AdminController
{
    public function dashboard(): void
    {
        Auth::requireAuth();
        Auth::requireRole('Admin', 'Moderator');

        View::render('admin/dashboard', [
            'title' => 'Operational Intelligence Dashboard',
            'stats' => (new AdminRepository())->dashboard(),
            'csrf' => Csrf::token(),
            'commentTags' => (new AdminRepository())->commentTags(),
        ]);
    }

    public function moderationQueue(): void
    {
        Auth::requireAuth();
        Auth::requireRole('Admin', 'Moderator');

        View::render('moderation/queue', [
            'title' => 'Moderation Queue',
            'queue' => (new AdminRepository())->moderationQueue(),
            'csrf' => Csrf::token(),
            'commentTags' => (new AdminRepository())->commentTags(),
        ]);
    }

    public function hideComment(int $id): void
    {
        Auth::requireAuth();
        Auth::requireRole('Admin', 'Moderator');
        if (!Csrf::check($_POST['_csrf'] ?? null)) {
            http_response_code(422);
            exit('Invalid CSRF token.');
        }
        (new AdminRepository())->setCommentVisibility($id, true, (int) Auth::user()['id']);
        Helpers::redirect('/admin/moderation');
    }

    public function unhideComment(int $id): void
    {
        Auth::requireAuth();
        Auth::requireRole('Admin', 'Moderator');
        if (!Csrf::check($_POST['_csrf'] ?? null)) {
            http_response_code(422);
            exit('Invalid CSRF token.');
        }
        (new AdminRepository())->setCommentVisibility($id, false, (int) Auth::user()['id']);
        Helpers::redirect('/admin/moderation');
    }

    public function tagComment(int $id): void
    {
        Auth::requireAuth();
        Auth::requireRole('Admin', 'Moderator');
        if (!Csrf::check($_POST['_csrf'] ?? null)) {
            http_response_code(422);
            exit('Invalid CSRF token.');
        }
        $tagId = (int) ($_POST['tag_id'] ?? 0);
        if ($tagId > 0) {
            (new AdminRepository())->tagComment($id, $tagId, (int) Auth::user()['id']);
        }
        Helpers::redirect('/admin/moderation');
    }

    public function bookmarklet(): void
    {
        Auth::requireAuth();
        Auth::requireRole('Admin');
        View::render('admin/bookmarklet', ['title' => 'Admin Bookmarklet']);
    }

    public function reports(): void
    {
        Auth::requireAuth();
        Auth::requireRole('Admin', 'Moderator');
        View::render('admin/reports', [
            'title' => 'Reports Queue',
            'queue' => (new AdminRepository())->moderationQueue(),
        ]);
    }

    public function usersBadges(): void
    {
        Auth::requireAuth();
        Auth::requireRole('Admin');
        $db = \Intranet\Core\Database::connection();
        $users = $db->query('SELECT u.id, u.display_name, u.email, GROUP_CONCAT(DISTINCT r.name) AS roles, GROUP_CONCAT(DISTINCT b.name) AS badges
                             FROM users u
                             LEFT JOIN user_roles ur ON ur.user_id = u.id
                             LEFT JOIN roles r ON r.id = ur.role_id
                             LEFT JOIN user_badges ub ON ub.user_id = u.id
                             LEFT JOIN badges b ON b.id = ub.badge_id
                             GROUP BY u.id ORDER BY u.created_at DESC LIMIT 100')->fetchAll();
        View::render('admin/users_badges', ['title' => 'User & Badge Management', 'users' => $users]);
    }
}
