<?php

declare(strict_types=1);

namespace Tests;

use Intranet\Modules\Shared\Services\PostStatusTaggingService;

final class PostStatusTaggingServiceTest extends TestCase
{
    public function testComputeAssignsExpectedTagsForHighVelocityPost(): void
    {
        $service = new PostStatusTaggingService();

        $tags = $service->compute([
            'created_at' => date(DATE_ATOM, strtotime('-2 hours')),
            'like_count' => 14,
            'comment_count' => 12,
            'favorite_count' => 15,
            'bookmark_count' => 12,
            'dislike_count' => 10,
            'report_count' => 6,
        ]);

        $this->assertSame(
            ['New', 'Trending', 'Rising', 'Popular', 'Hot', 'Discussed', 'Controversial', 'Needs Review'],
            $tags
        );
    }

    public function testComputeReturnsEmptyArrayForColdPost(): void
    {
        $service = new PostStatusTaggingService();

        $tags = $service->compute([
            'created_at' => date(DATE_ATOM, strtotime('-5 days')),
            'like_count' => 1,
            'comment_count' => 0,
            'favorite_count' => 0,
            'bookmark_count' => 0,
            'dislike_count' => 0,
            'report_count' => 0,
        ]);

        $this->assertSame([], $tags);
    }
}

