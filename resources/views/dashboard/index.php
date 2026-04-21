<?php

use Intranet\Core\Helpers;

$posts = $posts ?? [];
$newestPosts = array_slice($posts, 0, 4);
$trendingPosts = array_values(array_filter($posts, static function (array $post): bool {
    $status = strtolower((string) ($post['status_tags'] ?? ''));
    return str_contains($status, 'trending') || str_contains($status, 'hot') || str_contains($status, 'popular');
}));
$trendingPosts = array_slice($trendingPosts !== [] ? $trendingPosts : $posts, 0, 4);

$commentVolume = 0;
$bookmarkVolume = 0;
$missingThumbnailCount = 0;
$categoryCounts = [];

foreach ($posts as $post) {
    $commentVolume += (int) ($post['comment_count'] ?? 0);
    $bookmarkVolume += (int) ($post['bookmark_count'] ?? 0);
    if (empty($post['thumbnail_url'])) {
        $missingThumbnailCount++;
    }

    $category = (string) ($post['category_name'] ?? 'Uncategorized');
    $categoryCounts[$category] = ($categoryCounts[$category] ?? 0) + 1;
}

arsort($categoryCounts);
$dominantCategory = (string) array_key_first($categoryCounts);

$activityFeed = [];
foreach (array_slice($posts, 0, 5) as $index => $post) {
    $activityFeed[] = [
        'meta' => sprintf('Signal %02d', $index + 1),
        'body' => sprintf(
            '%s captured %d comments and %d bookmarks.',
            (string) ($post['title'] ?? 'Untitled signal'),
            (int) ($post['comment_count'] ?? 0),
            (int) ($post['bookmark_count'] ?? 0)
        ),
    ];
}

$alerts = [
    [
        'severity' => $missingThumbnailCount > 4 ? 'high' : 'medium',
        'title' => sprintf('%d posts missing thumbnails', $missingThumbnailCount),
        'copy' => 'Backfill source previews to keep scan speed high across the dashboard.',
    ],
    [
        'severity' => $bookmarkVolume > 12 ? 'low' : 'medium',
        'title' => sprintf('%d bookmark events across the visible grid', $bookmarkVolume),
        'copy' => 'High save activity usually means reference material worth tagging for fast retrieval.',
    ],
    [
        'severity' => $dominantCategory !== '' ? 'low' : 'medium',
        'title' => $dominantCategory !== '' ? 'Category gravity: ' . $dominantCategory : 'Category distribution incomplete',
        'copy' => 'If one stream dominates, spin out a new taxonomy branch before the queue becomes noisy.',
    ],
];
?>
<div class="page-shell">
    <section class="page-hero glass-panel">
        <div class="page-hero-grid">
            <div>
                <span class="eyebrow">Control Room</span>
                <h1 class="page-title">Investigative dashboard for live signals, hot content, and operator actions.</h1>
                <p class="page-copy">This home screen behaves like a compact intelligence floor: newest submissions, active chatter, and system pressure points in one dense scan path.</p>
            </div>
            <div class="page-meta">
                <span class="chip chip-status">Live Feed</span>
                <span class="chip chip-category">Server Rendered</span>
                <span class="chip chip-tag">Reusable Modules</span>
            </div>
        </div>
    </section>

    <section class="metric-grid">
        <article class="intel-stat-card">
            <div class="intel-stat-label">Total Posts</div>
            <div class="intel-stat-value"><?= count($posts) ?></div>
            <div class="intel-stat-foot">Visible intelligence records in the active grid.</div>
        </article>
        <article class="intel-stat-card">
            <div class="intel-stat-label">Comment Volume</div>
            <div class="intel-stat-value"><?= $commentVolume ?></div>
            <div class="intel-stat-foot">Conversation density across the current feed window.</div>
        </article>
        <article class="intel-stat-card">
            <div class="intel-stat-label">Thumbnail Gaps</div>
            <div class="intel-stat-value"><?= $missingThumbnailCount ?></div>
            <div class="intel-stat-foot">Assets still missing visual confirmation.</div>
        </article>
    </section>

    <div class="row g-3">
        <div class="col-12 col-xxl-8">
            <section class="glass-panel section-card">
                <div class="intel-panel-header">
                    <div>
                        <div class="panel-kicker">Newest Posts</div>
                        <h2 class="panel-title">Freshly ingested signals</h2>
                        <p class="panel-copy">High-density post cards built for quick triage, not blog browsing.</p>
                    </div>
                    <a class="btn btn-outline-light btn-sm" href="/submit">Add Signal</a>
                </div>
                <div class="row g-3">
                    <?php foreach ($newestPosts as $post): ?>
                        <div class="col-12 col-xl-6">
                            <?php require dirname(__DIR__) . '/components/post_card.php'; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>
        </div>

        <div class="col-12 col-xxl-4">
            <section class="glass-panel section-card h-100">
                <div class="intel-panel-header">
                    <div>
                        <div class="panel-kicker">Trending</div>
                        <h2 class="panel-title">Hot content sweep</h2>
                        <p class="panel-copy">Priority signals with the strongest engagement tags.</p>
                    </div>
                </div>
                <div class="intel-feed-list">
                    <?php foreach ($trendingPosts as $post): ?>
                        <a class="intel-feed-item text-decoration-none" href="/post/<?= (int) $post['id'] ?>">
                            <div class="intel-feed-meta"><?= Helpers::e($post['category_name'] ?? 'Uncategorized') ?></div>
                            <div class="intel-feed-body"><strong><?= Helpers::e($post['title']) ?></strong></div>
                            <div class="page-actions mt-2">
                                <?php foreach (array_slice(array_filter(array_map('trim', explode(',', (string) ($post['status_tags'] ?? '')))), 0, 3) as $chip): ?>
                                    <span class="chip chip-status"><?= Helpers::e($chip) ?></span>
                                <?php endforeach; ?>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            </section>
        </div>

        <div class="col-12 col-xl-6">
            <section class="glass-panel section-card">
                <div class="intel-panel-header">
                    <div>
                        <div class="panel-kicker">Activity Feed</div>
                        <h2 class="panel-title">Comment and save movement</h2>
                        <p class="panel-copy">Compact operator feed for volume spikes and chatter hotspots.</p>
                    </div>
                </div>
                <div class="intel-feed-list">
                    <?php foreach ($activityFeed as $item): ?>
                        <div class="intel-feed-item">
                            <div class="intel-feed-meta"><?= Helpers::e($item['meta']) ?></div>
                            <div class="intel-feed-body"><?= Helpers::e($item['body']) ?></div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>
        </div>

        <div class="col-12 col-xl-6">
            <section class="glass-panel section-card">
                <div class="intel-panel-header">
                    <div>
                        <div class="panel-kicker">Recommendations</div>
                        <h2 class="panel-title">System alerts and operator cues</h2>
                        <p class="panel-copy">Actionable notices shaped like evidence cards instead of generic admin notices.</p>
                    </div>
                </div>
                <div class="intel-alert-list">
                    <?php foreach ($alerts as $alert): ?>
                        <article class="intel-alert-card">
                            <span class="alert-severity severity-<?= Helpers::e($alert['severity']) ?>"></span>
                            <div class="flex-grow-1">
                                <div class="intel-feed-meta"><?= strtoupper($alert['severity']) ?> SEVERITY</div>
                                <div class="intel-feed-body"><strong><?= Helpers::e($alert['title']) ?></strong></div>
                                <div class="panel-copy mt-1"><?= Helpers::e($alert['copy']) ?></div>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            </section>
        </div>
    </div>
</div>
