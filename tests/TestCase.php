<?php

declare(strict_types=1);

namespace Tests;

abstract class TestCase
{
    protected function setUp(): void
    {
    }

    protected function tearDown(): void
    {
    }

    protected function assertTrue(bool $condition, string $message = 'Expected condition to be true.'): void
    {
        if (!$condition) {
            throw new \RuntimeException($message);
        }
    }

    protected function assertFalse(bool $condition, string $message = 'Expected condition to be false.'): void
    {
        if ($condition) {
            throw new \RuntimeException($message);
        }
    }

    protected function assertSame(mixed $expected, mixed $actual, string $message = ''): void
    {
        if ($expected !== $actual) {
            $suffix = $message !== '' ? " {$message}" : '';
            throw new \RuntimeException(
                'Failed asserting that values are identical.' . $suffix .
                "\nExpected: " . var_export($expected, true) .
                "\nActual: " . var_export($actual, true)
            );
        }
    }

    protected function assertContains(string $needle, string $haystack, string $message = ''): void
    {
        if (!str_contains($haystack, $needle)) {
            $suffix = $message !== '' ? " {$message}" : '';
            throw new \RuntimeException("Failed asserting that [$needle] exists in output.$suffix");
        }
    }

    protected function assertCount(int $expectedCount, array $items, string $message = ''): void
    {
        $this->assertSame($expectedCount, count($items), $message);
    }

    protected function assertThrows(callable $callback, string $expectedException, string $message = ''): void
    {
        try {
            $callback();
        } catch (\Throwable $throwable) {
            if ($throwable instanceof $expectedException) {
                return;
            }

            $suffix = $message !== '' ? " {$message}" : '';
            throw new \RuntimeException(
                "Expected exception $expectedException, got " . $throwable::class . ".$suffix"
            );
        }

        $suffix = $message !== '' ? " {$message}" : '';
        throw new \RuntimeException("Expected exception $expectedException was not thrown.$suffix");
    }

    protected function resetSession(): void
    {
        $_SESSION = [];
    }

    public function run(string $method): void
    {
        $this->setUp();

        try {
            $this->{$method}();
        } finally {
            $this->tearDown();
        }
    }
}

