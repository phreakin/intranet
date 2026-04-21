<?php

declare(strict_types=1);

namespace Tests;

final class RouteMapTest extends TestCase
{
    private function routeFile(): string
    {
        return (string) file_get_contents(__DIR__ . '/../public/index.php');
    }

    public function testCriticalPublicRoutesAreRegistered(): void
    {
        $routes = $this->routeFile();

        $expectedRoutes = [
            '$router->add(\'GET\', \'/\', static fn () => $dashboard->index());',
            '$router->add(\'GET\', \'/dashboard\', static fn () => $dashboard->index());',
            '$router->add(\'GET\', \'/dashboard/widgets/{widget}\', static fn (array $p) => $dashboard->widget($p[\'widget\']));',
            '$router->add(\'GET\', \'/submit\', static fn () => $post->createForm());',
            '$router->add(\'POST\', \'/submit\', static fn () => $post->create());',
            '$router->add(\'GET\', \'/post/{id}\', static fn (array $p) => $post->show((int) $p[\'id\']));',
            '$router->add(\'GET\', \'/category/{slug}\', static fn (array $p) => $post->category($p[\'slug\']));',
            '$router->add(\'GET\', \'/tag/{slug}\', static fn (array $p) => $post->tag($p[\'slug\']));',
            '$router->add(\'GET\', \'/favorites\', static fn () => $user->favorites());',
            '$router->add(\'GET\', \'/bookmarks\', static fn () => $user->bookmarks());',
            '$router->add(\'GET\', \'/pages/{slug}\', static fn (array $p) => $cms->showPage($p[\'slug\']));',
            '$router->add(\'GET\', \'/articles/{slug}\', static fn (array $p) => $cms->showArticle($p[\'slug\']));',
            '$router->add(\'GET\', \'/page/{slug}\', static fn (array $p) => $cms->showLegacyPage($p[\'slug\']));',
            '$router->add(\'GET\', \'/login\', static fn () => $auth->showLogin());',
            '$router->add(\'POST\', \'/login\', static fn () => $auth->login());',
            '$router->add(\'GET\', \'/register\', static fn () => $auth->showRegister());',
            '$router->add(\'POST\', \'/register\', static fn () => $auth->register());',
            '$router->add(\'POST\', \'/logout\', static fn () => $auth->logout());',
        ];

        foreach ($expectedRoutes as $route) {
            $this->assertContains($route, $routes, "Missing expected public route: {$route}");
        }
    }

    public function testCriticalAdminRoutesAreRegistered(): void
    {
        $routes = $this->routeFile();

        $expectedRoutes = [
            '$router->add(\'GET\', \'/admin\', static fn () => $admin->dashboard());',
            '$router->add(\'GET\', \'/admin/dashboard\', static fn () => $admin->dashboard());',
            '$router->add(\'GET\', \'/admin/moderation\', static fn () => $admin->moderationQueue());',
            '$router->add(\'GET\', \'/admin/reports\', static fn () => $admin->reports());',
            '$router->add(\'GET\', \'/admin/rbac\', static fn () => $adminRbac->index());',
            '$router->add(\'POST\', \'/admin/rbac/{id}\', static fn (array $p) => $adminRbac->updateRole((int) $p[\'id\']));',
            '$router->add(\'GET\', \'/admin/users-badges\', static fn () => $admin->usersBadges());',
            '$router->add(\'GET\', \'/admin/bookmarklet\', static fn () => $admin->bookmarklet());',
            '$router->add(\'GET\', \'/admin/cms/pages\', static fn () => $adminCms->pagesIndex());',
            '$router->add(\'GET\', \'/admin/cms/pages/new\', static fn () => $adminCms->pageNewForm());',
            '$router->add(\'POST\', \'/admin/cms/pages\', static fn () => $adminCms->pageCreate());',
            '$router->add(\'GET\', \'/admin/cms/blocks\', static fn () => $adminCms->blocksIndex());',
            '$router->add(\'POST\', \'/admin/cms/blocks\', static fn () => $adminCms->blockSave());',
            '$router->add(\'GET\', \'/admin/cms/menus\', static fn () => $adminCms->menusIndex());',
            '$router->add(\'POST\', \'/admin/cms/menus\', static fn () => $adminCms->menuCreate());',
        ];

        foreach ($expectedRoutes as $route) {
            $this->assertContains($route, $routes, "Missing expected admin route: {$route}");
        }
    }

    public function testRouterStillHasExpectedBreadth(): void
    {
        $routes = $this->routeFile();
        $routeCount = substr_count($routes, '$router->add(');

        $this->assertTrue($routeCount >= 50, 'Route file unexpectedly shrank. Review public/index.php.');
    }
}
