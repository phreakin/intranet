<?php

use Intranet\Core\Helpers;

$posts = $posts ?? [];
?>
<div class="intel-feed-list">
    <?php foreach ($posts as $post): ?>
        <a class="intel-feed-item text-decoration-none" href="/post/<?= (int) $post['id'] ?>">
            <div class="intel-feed-meta"><?= Helpers::e((string) ($post['category_name'] ?? 'Uncategorized')) ?></div>
            <div class="intel-feed-body"><strong><?= Helpers::e((string) ($post['title'] ?? 'Untitled signal')) ?></strong></div>
            <div class="panel-copy mt-1"><?= Helpers::e((string) ($post['description'] ?? 'No summary available.')) ?></div>
            <div class="stats-strip mt-2">
                <span class="stat-pill">Likes <?= (int) ($post['like_count'] ?? 0) ?></span>
                <span class="stat-pill">Comments <?= (int) ($post['comment_count'] ?? 0) ?></span>
                <span class="stat-pill">Bookmarks <?= (int) ($post['bookmark_count'] ?? 0) ?></span>
            </div>
        </a>
    <?php endforeach; ?>
</div>
