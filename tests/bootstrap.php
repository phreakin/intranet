<?php

declare(strict_types=1);

$autoloadPath = dirname(__DIR__) . '/vendor/autoload.php';
if (!is_file($autoloadPath)) {
    fwrite(STDERR, "Missing vendor/autoload.php. Run composer install first.\n");
    exit(1);
}

require $autoloadPath;

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_name('intranet_test');
    session_start();
}

$_SERVER['REQUEST_URI'] ??= '/';

