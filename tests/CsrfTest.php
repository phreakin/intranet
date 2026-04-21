<?php

declare(strict_types=1);

namespace Tests;

use Intranet\Core\Csrf;

final class CsrfTest extends TestCase
{
    protected function setUp(): void
    {
        $this->resetSession();
    }

    public function testTokenPersistsWithinSession(): void
    {
        $first = Csrf::token();
        $second = Csrf::token();

        $this->assertSame($first, $second);
        $this->assertSame(64, strlen($first));
    }

    public function testCheckAcceptsValidToken(): void
    {
        $token = Csrf::token();

        $this->assertTrue(Csrf::check($token));
    }

    public function testCheckRejectsMissingOrInvalidToken(): void
    {
        $token = Csrf::token();

        $this->assertFalse(Csrf::check(null));
        $this->assertFalse(Csrf::check($token . 'invalid'));
    }
}

