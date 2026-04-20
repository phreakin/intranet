<?php use Intranet\Core\Helpers; ?>
<div class="glass-panel p-4">
    <div class="d-flex align-items-center gap-3 mb-3">
        <img src="<?= Helpers::e($profile['avatar_url'] ?: 'https://placehold.co/80x80') ?>" alt="avatar" class="rounded-circle" width="80" height="80">
        <div>
            <h1 class="h3 mb-0"><?= Helpers::e($profile['display_name']) ?></h1>
            <div class="small text-secondary"><?= Helpers::e((string) $profile['roles']) ?></div>
        </div>
    </div>
    <p class="text-secondary"><?= Helpers::e((string) ($profile['bio'] ?? 'No bio yet.')) ?></p>
    <div class="d-flex gap-2 flex-wrap mb-3">
        <?php foreach (array_filter(explode(',', (string) ($profile['badges'] ?? ''))) as $badge): ?>
            <span class="badge chip-status"><?= Helpers::e($badge) ?></span>
        <?php endforeach; ?>
    </div>
    <div class="row g-2 text-center">
        <div class="col"><div class="glass-subpanel p-3"><strong><?= (int) $profile['post_count'] ?></strong><div class="small text-secondary">Posts</div></div></div>
        <div class="col"><div class="glass-subpanel p-3"><strong><?= (int) $profile['comment_count'] ?></strong><div class="small text-secondary">Comments</div></div></div>
        <div class="col"><div class="glass-subpanel p-3"><strong><?= (int) $profile['favorite_count'] ?></strong><div class="small text-secondary">Favorites</div></div></div>
        <div class="col"><div class="glass-subpanel p-3"><strong><?= (int) $profile['bookmark_count'] ?></strong><div class="small text-secondary">Bookmarks</div></div></div>
    </div>
</div>
