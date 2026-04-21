<?php use Intranet\Core\Helpers; ?>
<div class="page-shell">
    <section class="page-hero glass-panel">
        <div class="page-hero-grid">
            <div>
                <span class="eyebrow">Tag View</span>
                <h1 class="page-title">Tag: #<?= Helpers::e($slug) ?></h1>
                <p class="page-copy">Tagged intelligence stays in the same compact visual language used by the dashboard and moderation surfaces.</p>
            </div>
        </div>
    </section>

    <section class="glass-panel section-card">
        <div class="row g-3">
            <?php foreach (($posts ?? []) as $post): ?>
                <div class="col-12 col-xl-6">
                    <?php require dirname(__DIR__) . '/components/post_card.php'; ?>
                </div>
            <?php endforeach; ?>
            <?php if (($posts ?? []) === []): ?>
                <div class="col-12">
                    <div class="empty-state">No posts tagged with this label yet.</div>
                </div>
            <?php endif; ?>
        </div>
    </section>
</div>
