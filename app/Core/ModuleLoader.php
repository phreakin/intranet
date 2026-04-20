<?php

declare(strict_types=1);

namespace Intranet\Core;

final class ModuleLoader
{
    private array $modules = [];

    public function load(): void
    {
        $dir = dirname(__DIR__, 2) . '/modules';
        if (!is_dir($dir)) {
            return;
        }

        $dbState = (new ModuleRegistry())->dbState();
        foreach (glob($dir . '/*/module.php') ?: [] as $file) {
            $config = require $file;
            if (!is_array($config) || empty($config['name'])) {
                continue;
            }

            $name = (string) $config['name'];
            $enabled = (bool) ($config['enabled'] ?? true);
            if (array_key_exists($name, $dbState)) {
                $enabled = $dbState[$name];
            }
            if (!$enabled) {
                continue;
            }

            $config['enabled'] = true;
            $this->modules[$name] = $config;
        }
    }

    public function all(): array
    {
        return $this->modules;
    }
}
