<?php

declare(strict_types=1);

namespace Tests;

use Intranet\Core\Helpers;

final class HelpersTest extends TestCase
{
    public function testEscapesHtmlEntities(): void
    {
        $value = '<script>alert("x")</script>';

        $this->assertSame('&lt;script&gt;alert(&quot;x&quot;)&lt;/script&gt;', Helpers::e($value));
    }

    public function testEscapesNullAsEmptyString(): void
    {
        $this->assertSame('', Helpers::e(null));
    }
}

