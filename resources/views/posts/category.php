<?php use Intranet\Core\Helpers; ?>
<div class="glass-panel p-4">
    <h1 class="h4">Category: <?= Helpers::e($slug) ?></h1>
    <ul class="list-group list-group-flush">
        <?php foreach (($posts ?? []) as $post): ?>
            <li class="list-group-item bg-transparent border-secondary d-flex justify-content-between align-items-center">
                <a href="/post/<?= (int) $post['id'] ?>" class="text-light"><?= Helpers::e($post['title']) ?></a>
                <div class="small text-secondary"><?= Helpers::e((string) $post['status_tags']) ?></div>
            </li>
        <?php endforeach; ?>
    </ul>
</div>
