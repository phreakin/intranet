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
<html lang="en" data-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= Helpers::e(($title ?? '') . ' · ' . $appName) ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@latest/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@latest/css/all.min.css">
    <link rel="stylesheet" href="/assets/css/tailwind.css">
    <link rel="stylesheet" href="/assets/css/theme.css">
</head>
<body class="intel-body">
<div class="intel-shell" data-sidebar-state="expanded" data-sidebar-open="false">
    <aside class="intel-sidebar glass-shell">
        <div class="d-flex align-items-center justify-content-between gap-2">
            <a class="intel-brand text-decoration-none" href="/">
                <span class="intel-brand-mark">IQ</span>
                <span class="intel-brand-copy">
                    <span class="intel-brand-title"><?= Helpers::e($appName) ?></span>
                    <span class="intel-brand-subtitle">Forensic intranet grid</span>
                </span>
            </a>
            <button class="intel-sidebar-toggle" type="button" data-sidebar-toggle aria-label="Toggle sidebar">||</button>
        </div>

        <div>
            <p class="intel-nav-heading">Operational Views</p>
            <nav class="intel-nav">
                <?php foreach ($navItems as $item): ?>
                    <?php if (in_array($item['label'], ['Moderation', 'Admin', 'Settings'], true) && !$isStaff): ?>
                        <?php continue; ?>
                    <?php endif; ?>
                    <a
                        class="intel-nav-link <?= $isActive($path, $item['match']) ? 'active' : '' ?>"
                        href="<?= Helpers::e($item['href']) ?>"
                        title="<?= Helpers::e($item['label']) ?>"
                    >
                        <span class="nav-icon"><?= Helpers::e($item['short']) ?></span>
                        <span class="nav-label"><?= Helpers::e($item['label']) ?></span>
                    </a>
                <?php endforeach; ?>
            </nav>
        </div>

        <div class="intel-sidebar-footer">
            <div class="intel-nav-heading mb-2">System Status</div>
            <div class="small text-secondary">Single-node private runtime</div>
            <div class="page-actions mt-3">
                <span class="chip chip-success">Server Rendered</span>
                <span class="chip chip-neutral">Low JS</span>
            </div>
        </div>
    </aside>

    <div class="intel-main">
        <header class="intel-topbar glass-panel">
            <div class="intel-topbar-group">
                <button class="intel-sidebar-toggle intel-mobile-sidebar-toggle" type="button" data-sidebar-mobile-toggle aria-label="Open navigation">[]</button>
                <form class="intel-search" method="get" action="/">
                    <div class="input-group">
                        <span class="input-group-text">Search</span>
                        <input
                            class="form-control"
                            type="search"
                            name="q"
                            value="<?= Helpers::e((string) ($_GET['q'] ?? '')) ?>"
                            placeholder="Search posts, tags, signals, investigations"
                            aria-label="Global search"
                        >
                    </div>
                </form>
            </div>

            <div class="intel-topbar-actions">
                <?php if ($currentUser): ?>
                    <a class="btn btn-primary btn-sm" href="/submit">Submit Link</a>
                    <a class="btn btn-outline-light btn-sm" href="/favorites">Favorites</a>
                    <a class="btn btn-outline-light btn-sm" href="/bookmarks">Bookmarks</a>
                    <?php if ($isStaff): ?>
                        <a class="btn btn-outline-info btn-sm" href="/admin">Control Room</a>
                    <?php endif; ?>
                    <button class="btn btn-ghost btn-icon btn-sm" type="button" aria-label="Notifications">NT</button>
                    <div class="dropdown">
                        <button class="btn avatar-pill dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <span class="avatar-mark"><?= Helpers::e($identityInitials) ?></span>
                            <span class="d-none d-sm-inline"><?= Helpers::e($identity) ?></span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="/user/<?= (int) $currentUser['id'] ?>">Profile</a></li>
                            <?php if ($isStaff): ?>
                                <li><a class="dropdown-item" href="/admin">Admin Dashboard</a></li>
                            <?php endif; ?>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="post" action="/logout" class="px-2 py-1">
                                    <input type="hidden" name="_csrf" value="<?= Helpers::e(Csrf::token()) ?>">
                                    <button class="btn btn-outline-danger btn-sm w-100" type="submit">Logout</button>
                                </form>
                            </li>
                        </ul>
                    </div>
                <?php else: ?>
                    <a class="btn btn-outline-light btn-sm" href="/login">Login</a>
                    <a class="btn btn-primary btn-sm" href="/register">Register</a>
                <?php endif; ?>
            </div>
        </header>

        <main class="intel-content">
            <?= $content ?>
        </main>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="/assets/js/app.js"></script>
</body>
</html>
