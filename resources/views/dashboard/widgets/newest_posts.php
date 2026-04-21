<?php $posts = $posts ?? []; ?>
<div class="grid grid-cols-1 gap-4 xl:grid-cols-2">
    <?php foreach ($posts as $post): ?>
        <div class="glass-panel p-3">
            <?php require dirname(__DIR__, 2) . '/components/post_card.php'; ?>
        </div>
    <?php endforeach; ?>
</div>
