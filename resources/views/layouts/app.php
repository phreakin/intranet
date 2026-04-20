<?php

use Intranet\Core\Helpers;

ob_start();
require $templatePath;
$content = ob_get_clean();
?>
<!doctype html>
<html lang="en" data-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= Helpers::e(($title ?? '') . ' · ' . $appName) ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="/assets/css/theme.css">
</head>
<body class="intel-body">
<nav class="navbar navbar-expand-lg glass-nav sticky-top">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold" href="/"><?= Helpers::e($appName) ?></a>
        <div class="d-flex align-items-center gap-2 ms-auto flex-wrap">
            <a class="btn btn-sm btn-outline-light" href="/submit">Submit</a>
            <a class="btn btn-sm btn-outline-light" href="/favorites">Favorites</a>
            <a class="btn btn-sm btn-outline-light" href="/bookmarks">Bookmarks</a>
            <?php if ($currentUser): ?>
                <a class="btn btn-sm btn-outline-info" href="/user/<?= (int) $currentUser['id'] ?>">Profile</a>
                <?php if (str_contains(strtolower((string) ($currentUser['roles'] ?? '')), 'admin') || str_contains(strtolower((string) ($currentUser['roles'] ?? '')), 'moderator')): ?>
                    <a class="btn btn-sm btn-primary" href="/admin">Admin</a>
                <?php endif; ?>
                <form method="post" action="/logout" class="d-inline">
                    <input type="hidden" name="_csrf" value="<?= Helpers::e(\Intranet\Core\Csrf::token()) ?>">
                    <button class="btn btn-sm btn-danger" type="submit">Logout</button>
                </form>
            <?php else: ?>
                <a class="btn btn-sm btn-light" href="/login">Login</a>
                <a class="btn btn-sm btn-outline-light" href="/register">Register</a>
            <?php endif; ?>
        </div>
    </div>
</nav>
<main class="container-fluid p-3 p-lg-4">
    <?= $content ?>
</main>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="/assets/js/app.js"></script>
</body>
</html>
