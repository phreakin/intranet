<?php use Intranet\Core\Helpers; ?>
<div class="glass-panel p-4">
    <h1 class="h4">Bookmarks</h1>
    <ul class="list-group list-group-flush">
        <?php foreach (($posts ?? []) as $post): ?>
            <li class="list-group-item bg-transparent border-secondary"><a href="/post/<?= (int) $post['id'] ?>" class="text-light"><?= Helpers::e($post['title']) ?></a></li>
        <?php endforeach; ?>
    </ul>
</div>
