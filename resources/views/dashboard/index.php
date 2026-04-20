<?php use Intranet\Core\Helpers; ?>
<div class="glass-panel p-3 mb-3">
    <h1 class="h3 mb-1">Intranet Prompt Intelligence Feed</h1>
    <p class="text-secondary mb-0">Newest link intelligence with computed state chips and engagement telemetry.</p>
</div>
<div class="row g-3">
    <?php foreach (($posts ?? []) as $post): ?>
        <div class="col-12 col-xl-6">
            <div class="glass-panel p-3 h-100">
                <div class="d-flex gap-3">
                    <?php if (!empty($post['thumbnail_url'])): ?>
                        <img src="<?= Helpers::e($post['thumbnail_url']) ?>" alt="thumbnail" class="thumb rounded">
                    <?php endif; ?>
                    <div class="flex-grow-1">
                        <a class="h5 d-block text-decoration-none text-light" href="/post/<?= (int) $post['id'] ?>"><?= Helpers::e($post['title']) ?></a>
                        <p class="small text-secondary"><?= Helpers::e(mb_substr((string) ($post['description'] ?? ''), 0, 200)) ?></p>
                        <div class="d-flex flex-wrap gap-1 mb-2">
                            <?php foreach (array_filter(explode(',', (string) ($post['status_tags'] ?? ''))) as $chip): ?>
                                <span class="badge chip-status"><?= Helpers::e($chip) ?></span>
                            <?php endforeach; ?>
                        </div>
                        <div class="d-flex flex-wrap gap-2 small text-secondary">
                            <a class="chip-meta text-decoration-none text-light" href="/category/<?= Helpers::e(strtolower(trim(preg_replace('/[^a-z0-9]+/i', '-', (string) ($post['category_name'] ?? 'uncategorized')), '-'))) ?>"><?= Helpers::e($post['category_name'] ?? 'Uncategorized') ?></a>
                            <?php foreach (array_slice(array_filter(explode(',', (string) ($post['tags'] ?? ''))), 0, 6) as $tag): ?>
                                <a class="chip-meta text-decoration-none text-light" href="/tag/<?= Helpers::e(strtolower(trim(preg_replace('/[^a-z0-9]+/i', '-', $tag), '-'))) ?>">#<?= Helpers::e($tag) ?></a>
                            <?php endforeach; ?>
                        </div>
                        <div class="d-flex gap-2 mt-2 small text-secondary">
                            <span>▲ <?= (int) $post['like_count'] ?></span>
                            <span>▼ <?= (int) $post['dislike_count'] ?></span>
                            <span>💬 <?= (int) $post['comment_count'] ?></span>
                            <span>★ <?= (int) $post['favorite_count'] ?></span>
                            <span>🔖 <?= (int) $post['bookmark_count'] ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>
