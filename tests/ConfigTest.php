<?php

declare(strict_types=1);

namespace Tests;

use Intranet\Core\Config;

final class ConfigTest extends TestCase
{
    protected function tearDown(): void
    {
        putenv('APP_NAME');
        putenv('APP_DEBUG');
        $this->resetConfigCache();
    }

    public function testReturnsEnvironmentOverrideForAppName(): void
    {
        putenv('APP_NAME=Test Intranet');
        $this->resetConfigCache();

        $this->assertSame('Test Intranet', Config::get('app', 'name'));
    }

    public function testReturnsDefaultForMissingConfigKey(): void
    {
        $this->resetConfigCache();

        $this->assertSame('fallback-value', Config::get('app', 'missing_key', 'fallback-value'));
    }

    public function testReturnsWholeConfigArray(): void
    {
        $this->resetConfigCache();

        $config = Config::get('features');

        $this->assertTrue(is_array($config), 'Expected features config to be an array.');
        $this->assertSame(true, $config['oauth']);
    }

    private function resetConfigCache(): void
    {
        $reflection = new \ReflectionClass(Config::class);
        $property = $reflection->getProperty('cache');
        $property->setAccessible(true);
        $property->setValue([]);
    }
}

