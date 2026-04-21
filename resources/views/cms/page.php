<?php use Intranet\Core\Helpers; ?>
<div class="row g-3">
    <div class="col-lg-8">
        <article class="glass-panel p-4">
            <header class="mb-3">
                <div class="page-actions mb-2">
                    <span class="chip chip-category"><?= Helpers::e(ucfirst((string) ($contentType ?? 'page'))) ?></span>
                    <?php if (!empty($page['status'])): ?>
                        <span class="chip chip-status"><?= Helpers::e((string) $page['status']) ?></span>
                    <?php endif; ?>
                </div>
                <h1 class="h3 mb-1"><?= Helpers::e($page['title']) ?></h1>
                <?php if (!empty($page['summary'])): ?>
                    <p class="text-secondary mb-0"><?= Helpers::e($page['summary']) ?></p>
                <?php endif; ?>
                <?php if (!empty($page['published_at'])): ?>
                    <div class="small text-secondary mt-2">Published <?= Helpers::e((string) $page['published_at']) ?></div>
                <?php endif; ?>
            </header>
            <div class="cms-body">
                <?= $page['body'] /* Admin-authored; trusted HTML per spec */ ?>
            </div>
        </article>
    </div>
    <?php if (!empty($sidebar)): ?>
        <aside class="col-lg-4">
            <div class="glass-panel p-3">
                <h2 class="h6 text-uppercase text-secondary"><?= Helpers::e($sidebar['label']) ?></h2>
                <div class="small"><?= $sidebar['content'] ?></div>
            </div>
        </aside>
    <?php endif; ?>
</div>
