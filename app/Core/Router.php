<?php

declare(strict_types=1);

namespace Intranet\Core;

final class Router
{
    private array $routes = [];

    public function add(string $method, string $path, callable $handler): void
    {
        $this->routes[] = [$method, $path, $handler];
    }

    public function dispatch(string $method, string $uri): void
    {
        $path = parse_url($uri, PHP_URL_PATH) ?: '/';
        foreach ($this->routes as [$routeMethod, $routePath, $handler]) {
            if ($routeMethod !== $method) {
                continue;
            }

            $pattern = '#^' . preg_replace('#\{([a-zA-Z_][a-zA-Z0-9_]*)\}#', '(?P<$1>[^/]+)', $routePath) . '$#';
            if (!preg_match($pattern, $path, $matches)) {
                continue;
            }

            $params = array_filter($matches, static fn ($key): bool => !is_int($key), ARRAY_FILTER_USE_KEY);
            $handler($params);
            return;
        }

        http_response_code(404);
        View::render('errors/404', ['title' => 'Not Found']);
    }
}
