<?php

use Intranet\Core\Helpers;

$slugify = static function (?string $value): string {
    $value = strtolower(trim((string) $value));
    $value = preg_replace('/[^a-z0-9]+/i', '-', $value ?? '') ?? '';
    return trim($value, '-') ?: 'unknown';
};

$statusTags = array_values(array_filter(array_map('trim', explode(',', (string) ($post['status_tags'] ?? '')))));
$postTags = array_slice(array_values(array_filter(array_map('trim', explode(',', (string) ($post['tags'] ?? ''))))), 0, 5);
$description = trim((string) ($post['description'] ?? ''));
?>
<article class="intel-post-card">
    <div class="intel-post-layout">
        <div>
            <?php if (!empty($post['thumbnail_url'])): ?>
                <img src="<?= Helpers::e($post['thumbnail_url']) ?>" alt="" class="intel-post-thumb">
            <?php else: ?>
                <div class="intel-post-thumb-placeholder">No Preview</div>
            <?php endif; ?>
        </div>

        <div class="min-w-0">
            <div class="intel-post-topline">
                <a class="chip chip-category" href="/category/<?= Helpers::e($slugify($post['category_name'] ?? 'uncategorized')) ?>">
                    <?= Helpers::e($post['category_name'] ?? 'Uncategorized') ?>
                </a>
                <?php if (!empty($post['site_name'])): ?>
                    <span class="intel-mini-meta"><?= Helpers::e($post['site_name']) ?></span>
                <?php endif; ?>
            </div>

            <a class="intel-post-title stretched-link" href="/post/<?= (int) $post['id'] ?>">
                <?= Helpers::e($post['title'] ?? 'Untitled signal') ?>
            </a>

            <?php if ($description !== ''): ?>
                <p class="intel-post-copy"><?= Helpers::e(mb_strimwidth($description, 0, 220, '...')) ?></p>
            <?php endif; ?>

            <?php if ($statusTags !== []): ?>
                <div class="intel-chip-row mb-2">
                    <?php foreach ($statusTags as $chip): ?>
                        <span class="chip chip-status"><?= Helpers::e($chip) ?></span>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <?php if ($postTags !== []): ?>
                <div class="intel-chip-row mb-3">
                    <?php foreach ($postTags as $tag): ?>
                        <a class="chip chip-tag position-relative z-2" href="/tag/<?= Helpers::e($slugify($tag)) ?>">#<?= Helpers::e($tag) ?></a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <div class="stats-strip">
                <span class="stat-pill">Likes <?= (int) ($post['like_count'] ?? 0) ?></span>
                <span class="stat-pill">Comments <?= (int) ($post['comment_count'] ?? 0) ?></span>
                <span class="stat-pill">Bookmarks <?= (int) ($post['bookmark_count'] ?? 0) ?></span>
            </div>
        </div>
    </div>
</article>
