<?php

declare(strict_types=1);

namespace Intranet\Core;

use Throwable;

final class ModuleRegistry
{
    public function dbState(): array
    {
        try {
            $rows = Database::connection()->query('SELECT module_name, enabled FROM module_registry')->fetchAll();
        } catch (Throwable) {
            return [];
        }

        $state = [];
        foreach ($rows as $row) {
            $state[(string) $row['module_name']] = (bool) ((int) ($row['enabled'] ?? 0));
        }

        return $state;
    }
}
