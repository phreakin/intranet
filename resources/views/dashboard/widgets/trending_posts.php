<?php

use Intranet\Core\Helpers;

$posts = $posts ?? [];
?>
<div class="flex flex-col gap-3">
    <?php foreach ($posts as $post): ?>
        <a href="/post/<?= (int) $post['id'] ?>"
           class="block rounded-xl border border-white/10 bg-white/5 p-3 transition hover:border-cyan-400/40 hover:bg-cyan-400/10">

            <div class="text-xs uppercase tracking-wide text-slate-400">
                <?= Helpers::e((string) ($post['category_name'] ?? 'Uncategorized')) ?>
            </div>

            <div class="text-sm font-semibold text-white mt-1">
                <?= Helpers::e((string) ($post['title'] ?? 'Untitled signal')) ?>
            </div>

            <div class="text-xs text-slate-400 mt-1">
                <?= Helpers::e((string) ($post['description'] ?? 'No summary available.')) ?>
            </div>

            <div class="flex gap-2 mt-2 text-xs text-slate-300">
                <span class="status-chip">👍 <?= (int) ($post['like_count'] ?? 0) ?></span>
                <span class="status-chip">💬 <?= (int) ($post['comment_count'] ?? 0) ?></span>
                <span class="status-chip">🔖 <?= (int) ($post['bookmark_count'] ?? 0) ?></span>
            </div>
        </a>
    <?php endforeach; ?>
</div>
