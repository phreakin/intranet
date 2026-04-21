<?php

declare(strict_types=1);

use Intranet\Core\Config;
use Intranet\Core\Router;
use Intranet\Modules\Admin\Controllers\AdminController;
use Intranet\Modules\Admin\Controllers\AdminRbacController;
use Intranet\Modules\Authentication\Controllers\AuthController;
use Intranet\Modules\Cms\Controllers\AdminCmsController;
use Intranet\Modules\Cms\Controllers\PageController;
use Intranet\Modules\Dashboard\Controllers\DashboardController;
use Intranet\Modules\Moderation\Controllers\ModerationController;
use Intranet\Modules\Posts\Controllers\PostController;
use Intranet\Modules\Users\Controllers\UserController;

require dirname(__DIR__) . '/bootstrap.php';

session_name(Config::get('app', 'session_name', 'intranet_prompt'));
session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'domain' => '',
    'secure' => (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'),
    'httponly' => true,
    'samesite' => 'Lax',
]);
session_start();

$router = new Router();
$dashboard = new DashboardController();
$adminRbac = new AdminRbacController();
$auth = new AuthController();
$cms = new PageController();
$adminCms = new AdminCmsController();
$post = new PostController();
$user = new UserController();
$admin = new AdminController();

$router->add('GET', '/', static fn () => $dashboard->index());
$router->add('GET', '/dashboard', static fn () => $dashboard->index());
$router->add('GET', '/dashboard/widgets/{widget}', static fn (array $p) => $dashboard->widget($p['widget']));
$router->add('GET', '/pages/{slug}', static fn (array $p) => $cms->showPage($p['slug']));
$router->add('GET', '/articles/{slug}', static fn (array $p) => $cms->showArticle($p['slug']));
$router->add('GET', '/page/{slug}', static fn (array $p) => $cms->showLegacyPage($p['slug']));
$router->add('GET', '/login', static fn () => $auth->showLogin());
$router->add('POST', '/login', static fn () => $auth->login());
$router->add('GET', '/register', static fn () => $auth->showRegister());
$router->add('POST', '/register', static fn () => $auth->register());
$router->add('POST', '/logout', static fn () => $auth->logout());
$router->add('GET', '/auth/oauth/{provider}', static fn (array $p) => $auth->oauthRedirect($p['provider']));
$router->add('GET', '/auth/oauth/{provider}/callback', static fn (array $p) => $auth->oauthCallback($p['provider']));

$router->add('GET', '/submit', static fn () => $post->createForm());
$router->add('POST', '/submit', static fn () => $post->create());
$router->add('GET', '/post/{id}', static fn (array $p) => $post->show((int) $p['id']));
$router->add('GET', '/post/{id}/edit', static fn (array $p) => $post->editForm((int) $p['id']));
$router->add('POST', '/post/{id}/edit', static fn (array $p) => $post->edit((int) $p['id']));
$router->add('POST', '/post/{id}/comment', static fn (array $p) => $post->comment((int) $p['id']));
$router->add('POST', '/post/{id}/favorite', static fn (array $p) => $post->favorite((int) $p['id']));
$router->add('POST', '/post/{id}/bookmark', static fn (array $p) => $post->bookmark((int) $p['id']));
$router->add('POST', '/post/{id}/like', static fn (array $p) => $post->like((int) $p['id']));
$router->add('POST', '/post/{id}/dislike', static fn (array $p) => $post->dislike((int) $p['id']));
$router->add('POST', '/post/{id}/report', static fn (array $p) => $post->report((int) $p['id']));
$router->add('POST', '/post/{id}/comments/{commentId}/report', static fn (array $p) => $post->reportComment((int) $p['id'], (int) $p['commentId']));
$router->add('GET', '/category/{slug}', static fn (array $p) => $post->category($p['slug']));
$router->add('GET', '/tag/{slug}', static fn (array $p) => $post->tag($p['slug']));

$router->add('GET', '/user/{id}', static fn (array $p) => $user->profile((int) $p['id']));
$router->add('GET', '/favorites', static fn () => $user->favorites());
$router->add('GET', '/bookmarks', static fn () => $user->bookmarks());

$router->add('GET', '/admin', static fn () => $admin->dashboard());
$router->add('GET', '/admin/dashboard', static fn () => $admin->dashboard());
$router->add('GET', '/admin/moderation', static fn () => $admin->moderationQueue());
$router->add('GET', '/admin/reports', static fn () => $admin->reports());
$router->add('GET', '/admin/rbac', static fn () => $adminRbac->index());
$router->add('POST', '/admin/rbac/{id}', static fn (array $p) => $adminRbac->updateRole((int) $p['id']));
$router->add('GET', '/admin/users-badges', static fn () => $admin->usersBadges());
$router->add('GET', '/admin/posts/{id}/edit', static fn (array $p) => $admin->editPostForm((int) $p['id']));
$router->add('POST', '/admin/posts/{id}/edit', static fn (array $p) => $admin->editPost((int) $p['id']));
$router->add('POST', '/admin/posts/{id}/delete', static fn (array $p) => $admin->deletePost((int) $p['id']));
$router->add('POST', '/admin/categories', static fn () => $admin->createCategory());
$router->add('POST', '/admin/tags', static fn () => $admin->createTag());
$router->add('POST', '/admin/users/{id}/badge', static fn (array $p) => $admin->assignBadge((int) $p['id']));
$router->add('POST', '/admin/users/{id}/role', static fn (array $p) => $admin->assignRole((int) $p['id']));
$router->add('POST', '/admin/ai/{id}/review', static fn (array $p) => $admin->reviewAiLog((int) $p['id']));
$router->add('POST', '/admin/comments/{id}/hide', static fn (array $p) => $admin->hideComment((int) $p['id']));
$router->add('POST', '/admin/comments/{id}/unhide', static fn (array $p) => $admin->unhideComment((int) $p['id']));
$router->add('POST', '/admin/comments/{id}/tag', static fn (array $p) => $admin->tagComment((int) $p['id']));
$router->add('GET', '/admin/bookmarklet', static fn () => $admin->bookmarklet());
$router->add('GET', '/admin/cms/pages', static fn () => $adminCms->pagesIndex());
$router->add('GET', '/admin/cms/pages/new', static fn () => $adminCms->pageNewForm());
$router->add('POST', '/admin/cms/pages', static fn () => $adminCms->pageCreate());
$router->add('GET', '/admin/cms/pages/{id}/edit', static fn (array $p) => $adminCms->pageEditForm((int) $p['id']));
$router->add('POST', '/admin/cms/pages/{id}/edit', static fn (array $p) => $adminCms->pageUpdate((int) $p['id']));
$router->add('POST', '/admin/cms/pages/{id}/delete', static fn (array $p) => $adminCms->pageDelete((int) $p['id']));
$router->add('GET', '/admin/cms/blocks', static fn () => $adminCms->blocksIndex());
$router->add('POST', '/admin/cms/blocks', static fn () => $adminCms->blockSave());
$router->add('POST', '/admin/cms/blocks/{id}/delete', static fn (array $p) => $adminCms->blockDelete((int) $p['id']));
$router->add('GET', '/admin/cms/menus', static fn () => $adminCms->menusIndex());
$router->add('POST', '/admin/cms/menus', static fn () => $adminCms->menuCreate());
$router->add('POST', '/admin/cms/menus/{id}/delete', static fn (array $p) => $adminCms->menuDelete((int) $p['id']));
$router->add('POST', '/admin/cms/menus/{id}/items', static fn (array $p) => $adminCms->menuItemCreate((int) $p['id']));
$router->add('POST', '/admin/cms/menu-items/{id}/delete', static fn (array $p) => $adminCms->menuItemDelete((int) $p['id']));

$moderation = new ModerationController();

$router->add('GET', '/moderation', static fn () => $moderation->dashboard());
$router->add('GET', '/moderation/dashboard', static fn () => $moderation->dashboard());
$router->add('GET', '/moderation/widget', static fn () => $moderation->widget());

$router->dispatch($_SERVER['REQUEST_METHOD'] ?? 'GET', $_SERVER['REQUEST_URI'] ?? '/');
