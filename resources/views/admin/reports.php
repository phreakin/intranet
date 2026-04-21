<?php use Intranet\Core\Helpers; ?>
<div class="page-shell">
    <section class="page-hero glass-panel">
        <div class="page-hero-grid">
            <div>
                <span class="eyebrow">Reports Queue</span>
                <h1 class="page-title">Reported content staged as a compact threat queue instead of a plain list.</h1>
                <p class="page-copy">Post and comment reports share the same glass treatment, severity language, and expandable evidence posture.</p>
            </div>
            <div class="page-meta">
                <span class="chip chip-warning">Reported Posts</span>
                <span class="chip chip-moderation">Reported Comments</span>
            </div>
        </div>
    </section>

    <div class="row g-3">
        <div class="col-12 col-xl-6">
            <section class="glass-panel section-card">
                <div class="intel-panel-header">
                    <div>
                        <div class="panel-kicker">Post Reports</div>
                        <h2 class="panel-title">Content-level flags</h2>
                    </div>
                </div>
                <div class="intel-list-stack">
                    <?php foreach (($queue['post_reports'] ?? []) as $report): ?>
                        <article class="intel-list-item">
                            <div class="intel-list-meta">Post #<?= (int) $report['post_id'] ?></div>
                            <div class="intel-list-body"><strong><?= Helpers::e($report['title']) ?></strong></div>
                            <div class="panel-copy mt-1">Reason: <?= Helpers::e($report['reason']) ?></div>
                        </article>
                    <?php endforeach; ?>
                    <?php if (($queue['post_reports'] ?? []) === []): ?>
                        <div class="empty-state">No post reports waiting.</div>
                    <?php endif; ?>
                </div>
            </section>
        </div>

        <div class="col-12 col-xl-6">
            <section class="glass-panel section-card">
                <div class="intel-panel-header">
                    <div>
                        <div class="panel-kicker">Comment Reports</div>
                        <h2 class="panel-title">Conversation-level flags</h2>
                    </div>
                </div>
                <div class="intel-list-stack">
                    <?php foreach (($queue['comment_reports'] ?? []) as $report): ?>
                        <article class="intel-list-item">
                            <div class="intel-list-meta">Comment #<?= (int) $report['comment_id'] ?></div>
                            <div class="intel-list-body"><strong>Flagged reply in review</strong></div>
                            <div class="panel-copy mt-1">Reason: <?= Helpers::e($report['reason']) ?></div>
                        </article>
                    <?php endforeach; ?>
                    <?php if (($queue['comment_reports'] ?? []) === []): ?>
                        <div class="empty-state">No comment reports waiting.</div>
                    <?php endif; ?>
                </div>
            </section>
        </div>
    </div>
</div>
