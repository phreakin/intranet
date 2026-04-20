<?php

declare(strict_types=1);

namespace Intranet\Modules\Shared\Services;

final class PostStatusTaggingService
{
    public function compute(array $post): array
    {
        $tags = [];
        $createdAt = new \DateTimeImmutable((string) ($post['created_at'] ?? 'now'));
        $hoursOld = max(1, (int) floor((time() - $createdAt->getTimestamp()) / 3600));

        $likes = (int) ($post['like_count'] ?? 0);
        $comments = (int) ($post['comment_count'] ?? 0);
        $favorites = (int) ($post['favorite_count'] ?? 0);
        $bookmarks = (int) ($post['bookmark_count'] ?? 0);
        $dislikes = (int) ($post['dislike_count'] ?? 0);
        $reports = (int) ($post['report_count'] ?? 0);

        $engagement = $likes + $comments + $favorites + $bookmarks;
        $velocity = $engagement / $hoursOld;

        if ($hoursOld <= 24) {
            $tags[] = 'New';
        }
        if ($velocity >= 1.2) {
            $tags[] = 'Trending';
        }
        if ($velocity >= 2.2) {
            $tags[] = 'Rising';
        }
        if ($engagement >= 50) {
            $tags[] = 'Popular';
        }
        if ($engagement >= 20 && $hoursOld <= 48) {
            $tags[] = 'Hot';
        }
        if ($comments >= 10) {
            $tags[] = 'Discussed';
        }
        if ($likes >= 10 && $dislikes >= 8 && abs($likes - $dislikes) <= 5) {
            $tags[] = 'Controversial';
        }
        if ($reports >= 5) {
            $tags[] = 'Needs Review';
        }

        return array_values(array_unique($tags));
    }
}
