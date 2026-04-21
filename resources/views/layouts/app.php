<?php

use Intranet\Core\Csrf;
use Intranet\Core\Helpers;

ob_start();
require $templatePath;
$content = ob_get_clean();

$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$roles = strtolower((string) ($currentUser['roles'] ?? ''));
$isStaff = str_contains($roles, 'admin') || str_contains($roles, 'moderator');
$identity = trim((string) ($currentUser['display_name'] ?? $currentUser['email'] ?? 'Operator'));
$identityInitials = strtoupper(substr(preg_replace('/[^A-Z0-9]/i', '', $identity), 0, 2) ?: 'OP');
$navItems = [
    ['label' => 'Dashboard', 'short' => 'DB', 'href' => '/', 'match' => ['/']],
    ['label' => 'Posts', 'short' => 'PS', 'href' => '/', 'match' => ['/post/', '/category/', '/tag/']],
    ['label' => 'Categories', 'short' => 'CT', 'href' => '/admin', 'match' => ['/category/']],
    ['label' => 'Tags', 'short' => 'TG', 'href' => '/admin', 'match' => ['/tag/']],
    ['label' => 'Moderation', 'short' => 'MD', 'href' => '/admin/moderation', 'match' => ['/admin/moderation', '/admin/reports']],
    ['label' => 'Admin', 'short' => 'AD', 'href' => '/admin', 'match' => ['/admin']],
    ['label' => 'Settings', 'short' => 'ST', 'href' => '/admin/users-badges', 'match' => ['/admin/users-badges', '/user/']],
];

$isActive = static function (string $currentPath, array $matchers): bool {
    foreach ($matchers as $matcher) {
        if ($matcher === '/' && $currentPath === '/') {
            return true;
        }

        if ($matcher !== '/' && str_starts_with($currentPath, $matcher)) {
            return true;
        }
    }

    return false;
};
?>
<!doctype html>
<html lang="en" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= Helpers::e(($title ?? '') . ' · ' . $appName) ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@latest/css/all.min.css">
    <link rel="stylesheet" href="/assets/css/tailwind.css">
    <link rel="stylesheet" href="/assets/css/theme.css">
</head>
<body class="intel-body">
<div class="intel-shell" data-sidebar-state="expanded" data-sidebar-open="false">
    <aside class="intel-sidebar glass-shell">
        <div class="flex items-center justify-between gap-2">
            <a class="intel-brand" href="/">
                <span class="intel-brand-mark">IQ</span>
                <span class="intel-brand-copy flex flex-col">
                    <span class="intel-brand-title"><?= Helpers::e($appName) ?></span>
                    <span class="intel-brand-subtitle">Forensic intranet grid</span>
                </span>
            </a>
            <button class="intel-sidebar-toggle" type="button" data-sidebar-toggle>||</button>
        </div>

        <nav class="intel-nav mt-6">
            <?php foreach ($navItems as $item): ?>
                <?php if (in_array($item['label'], ['Moderation', 'Admin', 'Settings'], true) && !$isStaff) continue; ?>
                <a class="intel-nav-link <?= $isActive($path, $item['match']) ? 'active' : '' ?>" href="<?= Helpers::e($item['href']) ?>">
                    <span><?= Helpers::e($item['short']) ?></span>
                    <span><?= Helpers::e($item['label']) ?></span>
                </a>
            <?php endforeach; ?>
        </nav>
    </aside>

    <div class="intel-main">
        <header class="intel-topbar glass-panel flex items-center justify-between gap-4">
            <form class="w-full max-w-xl" method="get" action="/">
                <input class="search-input w-full" type="search" name="q" placeholder="Search intelligence...">
            </form>

            <div class="flex items-center gap-2">
                <?php if ($currentUser): ?>
                    <a class="px-3 py-2 bg-cyan-500 text-black rounded-lg" href="/submit">Submit</a>
                    <form method="post" action="/logout">
                        <input type="hidden" name="_csrf" value="<?= Helpers::e(Csrf::token()) ?>">
                        <button class="px-3 py-2 border border-red-500/40 text-red-300 rounded-lg">Logout</button>
                    </form>
                <?php else: ?>
                    <a class="px-3 py-2 border rounded-lg" href="/login">Login</a>
                <?php endif; ?>
            </div>
        </header>

        <main class="intel-content">
            <?= $content ?>
        </main>
    </div>
</div>
<script src="/assets/js/app.js"></script>
</body>
</html>
