<?php

declare(strict_types=1);

namespace Intranet\Modules\Admin\Controllers;

use Intranet\Core\Auth;
use Intranet\Core\Csrf;
use Intranet\Core\Database;
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
        $repo = new AdminRepository();
        View::render('admin/users_badges', [
            'title' => 'User & Badge Management',
            'users' => $repo->usersWithRolesBadges(),
            'badges' => $repo->allBadges(),
            'roles' => $repo->allRoles(),
            'csrf' => Csrf::token(),
        ]);
    }

    public function editPostForm(int $id): void
    {
        Auth::requireAuth();
        Auth::requireRole('Admin', 'Moderator');
        $repo = new AdminRepository();
        $post = $repo->findPost($id);
        if (!$post) {
            http_response_code(404);
            exit('Post not found');
        }
        View::render('posts/edit', [
            'title' => 'Admin Edit Post',
            'post' => $post,
            'categories' => Database::connection()->query('SELECT * FROM categories ORDER BY name ASC')->fetchAll(),
            'csrf' => Csrf::token(),
            'isAdminEdit' => true,
        ]);
    }

    public function editPost(int $id): void
    {
        Auth::requireAuth();
        Auth::requireRole('Admin', 'Moderator');
        if (!Csrf::check($_POST['_csrf'] ?? null)) {
            http_response_code(422);
            exit('Invalid CSRF token.');
        }
        $title = trim((string) ($_POST['title'] ?? ''));
        $description = trim((string) ($_POST['description'] ?? ''));
        $categoryId = (int) ($_POST['category_id'] ?? 0);
        $tags = array_map('trim', explode(',', (string) ($_POST['tags'] ?? '')));
        (new AdminRepository())->updatePostEditable($id, $title, $description, $categoryId > 0 ? $categoryId : null, $tags, (int) Auth::user()['id']);
        Helpers::redirect('/post/' . $id);
    }

    public function deletePost(int $id): void
    {
        Auth::requireAuth();
        Auth::requireRole('Admin');
        if (!Csrf::check($_POST['_csrf'] ?? null)) {
            http_response_code(422);
            exit('Invalid CSRF token.');
        }
        (new AdminRepository())->deletePost($id, (int) Auth::user()['id']);
        Helpers::redirect('/admin');
    }

    public function createCategory(): void
    {
        Auth::requireAuth();
        Auth::requireRole('Admin');
        if (!Csrf::check($_POST['_csrf'] ?? null)) {
            http_response_code(422);
            exit('Invalid CSRF token.');
        }
        (new AdminRepository())->createCategory((string) ($_POST['name'] ?? ''));
        Helpers::redirect('/admin');
    }

    public function createTag(): void
    {
        Auth::requireAuth();
        Auth::requireRole('Admin');
        if (!Csrf::check($_POST['_csrf'] ?? null)) {
            http_response_code(422);
            exit('Invalid CSRF token.');
        }
        (new AdminRepository())->createTag((string) ($_POST['name'] ?? ''));
        Helpers::redirect('/admin');
    }

    public function assignBadge(int $id): void
    {
        Auth::requireAuth();
        Auth::requireRole('Admin');
        if (!Csrf::check($_POST['_csrf'] ?? null)) {
            http_response_code(422);
            exit('Invalid CSRF token.');
        }
        $badgeId = (int) ($_POST['badge_id'] ?? 0);
        if ($badgeId > 0) {
            (new AdminRepository())->assignBadge($id, $badgeId, (int) Auth::user()['id']);
        }
        Helpers::redirect('/admin/users-badges');
    }

    public function assignRole(int $id): void
    {
        Auth::requireAuth();
        Auth::requireRole('Admin');
        if (!Csrf::check($_POST['_csrf'] ?? null)) {
            http_response_code(422);
            exit('Invalid CSRF token.');
        }
        $roleId = (int) ($_POST['role_id'] ?? 0);
        if ($roleId > 0) {
            (new AdminRepository())->assignRole($id, $roleId);
        }
        Helpers::redirect('/admin/users-badges');
    }

    public function reviewAiLog(int $id): void
    {
        Auth::requireAuth();
        Auth::requireRole('Admin', 'Moderator');
        if (!Csrf::check($_POST['_csrf'] ?? null)) {
            http_response_code(422);
            exit('Invalid CSRF token.');
        }
        $decision = (string) ($_POST['decision'] ?? 'rejected');
        if (!in_array($decision, ['accepted', 'rejected', 'overridden'], true)) {
            $decision = 'rejected';
        }
        (new AdminRepository())->reviewAiLog($id, $decision, (int) Auth::user()['id']);
        Helpers::redirect('/admin');
    }
}
