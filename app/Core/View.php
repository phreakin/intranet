<?php

declare(strict_types=1);

namespace Intranet\Core;

final class View
{
    public static function render(string $template, array $data = []): void
    {
        $templatePath = dirname(__DIR__, 2) . '/resources/views/' . $template . '.php';
        if (!is_file($templatePath)) {
            http_response_code(404);
            require dirname(__DIR__, 2) . '/resources/views/errors/404.php';
            return;
        }

        extract($data, EXTR_SKIP);
        $appName = Config::get('app', 'name', 'Intranet Prompt');
        $currentUser = Auth::user();
        $features = Config::get('features');
        require dirname(__DIR__, 2) . '/resources/views/layouts/app.php';
    }
}
