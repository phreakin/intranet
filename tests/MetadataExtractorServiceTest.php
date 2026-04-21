<?php

declare(strict_types=1);

namespace Tests;

use Intranet\Modules\Shared\Services\MetadataExtractorService;

final class MetadataExtractorServiceTest extends TestCase
{
    public function testExtractRejectsInvalidUrl(): void
    {
        $service = new MetadataExtractorService();

        $this->assertThrows(
            static fn (): array => $service->extract('not-a-url'),
            \InvalidArgumentException::class
        );
    }

    public function testSafeFetchUrlRejectsLocalhost(): void
    {
        $service = new MetadataExtractorService();
        $method = new \ReflectionMethod($service, 'isSafeFetchUrl');
        $method->setAccessible(true);

        $result = $method->invoke($service, 'http://localhost/test');

        $this->assertFalse($result);
    }

    public function testSafeFetchUrlRejectsUnsupportedScheme(): void
    {
        $service = new MetadataExtractorService();
        $method = new \ReflectionMethod($service, 'isSafeFetchUrl');
        $method->setAccessible(true);

        $result = $method->invoke($service, 'ftp://example.com/file');

        $this->assertFalse($result);
    }
}

