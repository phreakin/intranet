<?php

declare(strict_types=1);

namespace Tests;

use Intranet\Core\Router;

final class RouterTest extends TestCase
{
    protected function setUp(): void
    {
        http_response_code(200);
        $_SERVER['REQUEST_URI'] = '/';
        $this->resetSession();
    }

    protected function tearDown(): void
    {
        http_response_code(200);
        $_SERVER['REQUEST_URI'] = '/';
    }

    public function testDispatchPassesNamedParametersToHandler(): void
    {
        $router = new Router();
        $captured = null;

        $router->add('GET', '/post/{id}', static function (array $params) use (&$captured): void {
            $captured = $params;
        });

        $router->dispatch('GET', '/post/42?source=test');

        $this->assertSame(['id' => '42'], $captured);
    }

    public function testDispatchRenders404ForMissingRoute(): void
    {
        $router = new Router();
        $_SERVER['REQUEST_URI'] = '/missing';

        ob_start();
        $router->dispatch('GET', '/missing');
        $output = (string) ob_get_clean();

        $this->assertSame(404, http_response_code());
        $this->assertContains('Signal Lost', $output);
        $this->assertContains('Not Found ·', $output);
    }
}
