<?php

declare(strict_types=1);

/**
 * Lightweight migration runner.
 *
 * Reads database/migrations/*.sql in lexical order and runs each through the
 * `mysql` CLI client (so DELIMITER and stored-procedure syntax work). Tracks
 * applied files in a `_migrations` table so it is safe to re-run.
 *
 * Usage:
 *   composer migrate           # apply pending migrations
 *   composer migrate -- --fresh   # drop + recreate database, then apply all
 *   composer migrate -- --dry     # show pending migrations without running
 */

require dirname(__DIR__) . '/bootstrap.php';

use Intranet\Core\Config;
use Intranet\Core\Database;

$argv = $_SERVER['argv'] ?? [];
$flags = array_filter($argv, static fn(string $a): bool => str_starts_with($a, '--'));
$fresh = in_array('--fresh', $flags, true);
$dry   = in_array('--dry',   $flags, true);

$cfg = Config::get('database');
$dbName = (string) $cfg['name'];

$mysql = static function (array $args, ?string $sql = null) use ($cfg): int {
    $cmd = ['mysql',
        '--host=' . escapeshellarg((string) $cfg['host']),
        '--port=' . (int) $cfg['port'],
        '--user=' . escapeshellarg((string) $cfg['user']),
        '--default-character-set=' . escapeshellarg((string) $cfg['charset']),
    ];
    if ((string) $cfg['pass'] !== '') {
        // Use MYSQL_PWD env so password never lands in argv / process list.
        putenv('MYSQL_PWD=' . (string) $cfg['pass']);
    }
    $cmd = array_merge($cmd, $args);
    $line = implode(' ', $cmd);

    $descriptors = [0 => ['pipe', 'r'], 1 => STDOUT, 2 => STDERR];
    $proc = proc_open($line, $descriptors, $pipes);
    if (!is_resource($proc)) {
        return 1;
    }
    if ($sql !== null) {
        fwrite($pipes[0], $sql);
    }
    fclose($pipes[0]);
    return proc_close($proc);
};

if ($fresh) {
    fwrite(STDOUT, "[migrate] --fresh: dropping and recreating `{$dbName}`\n");
    $sql = "DROP DATABASE IF EXISTS `{$dbName}`; "
         . "CREATE DATABASE `{$dbName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;";
    if ($mysql([], $sql) !== 0) {
        fwrite(STDERR, "[migrate] failed to recreate database\n");
        exit(1);
    }
}

// Make sure the database exists (idempotent).
$mysql([], "CREATE DATABASE IF NOT EXISTS `{$dbName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");

// Connect via PDO for tracking, now that the DB exists.
$pdo = Database::connection();
$pdo->exec("CREATE TABLE IF NOT EXISTS `_migrations` (
    `name` VARCHAR(190) NOT NULL PRIMARY KEY,
    `applied_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

$applied = $pdo->query('SELECT name FROM _migrations')->fetchAll(\PDO::FETCH_COLUMN) ?: [];
$applied = array_flip(array_map('strval', $applied));

$dir = dirname(__DIR__) . '/database/migrations';
$files = glob($dir . '/*.sql') ?: [];
sort($files, SORT_NATURAL);

$pending = array_values(array_filter($files, static fn(string $f) => !isset($applied[basename($f)])));
if ($pending === []) {
    fwrite(STDOUT, "[migrate] nothing to do — schema is up to date\n");
    exit(0);
}

fwrite(STDOUT, "[migrate] " . count($pending) . " pending:\n");
foreach ($pending as $f) {
    fwrite(STDOUT, "  - " . basename($f) . "\n");
}
if ($dry) {
    fwrite(STDOUT, "[migrate] --dry: stopping without applying\n");
    exit(0);
}

foreach ($pending as $file) {
    $name = basename($file);
    fwrite(STDOUT, "[migrate] applying {$name}...\n");
    $sql = file_get_contents($file);
    if ($sql === false) {
        fwrite(STDERR, "[migrate] could not read {$file}\n");
        exit(1);
    }
    $code = $mysql(['--database=' . escapeshellarg($dbName)], $sql);
    if ($code !== 0) {
        fwrite(STDERR, "[migrate] {$name} FAILED (exit {$code}); halting\n");
        exit($code);
    }
    $stmt = $pdo->prepare('INSERT INTO _migrations (name) VALUES (:n)');
    $stmt->execute(['n' => $name]);
    fwrite(STDOUT, "[migrate] {$name} ok\n");
}

fwrite(STDOUT, "[migrate] done\n");
exit(0);
