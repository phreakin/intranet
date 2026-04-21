<?php $posts = $posts ?? []; ?>
<div class="dashboard-widget-posts">
    <div class="row g-3">
        <?php foreach ($posts as $post): ?>
            <div class="col-12 col-xl-6">
                <?php require dirname(__DIR__, 2) . '/components/post_card.php'; ?>
            </div>
        <?php endforeach; ?>
    </div>
</div>
