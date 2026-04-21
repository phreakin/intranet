<div class="intel-list">
    <?php if (empty($comments)): ?>
        <div class="empty-state">No reported comments found.</div>
    <?php else: ?>
        <?php foreach ($comments as $comment): ?>
            <article class="intel-item">
                <div class="intel-item-main">
                    <h3 class="intel-item-title">
                        Comment #<?= (int) $comment['id']; ?> on Post #<?= (int) $comment['post_id']; ?>
                    </h3>
                    <p class="intel-item-text">
                        <?= htmlspecialchars(mb_strimwidth((string) ($comment['content'] ?? ''), 0, 180, '...')); ?>
                    </p>
                    <div class="intel-meta-row">
                        <span class="status-chip reported">Reported</span>
                        <span class="telemetry-pill">Reports: <?= (int) ($comment['report_count'] ?? 0); ?></span>
                        <span class="telemetry-pill">Last: <?= htmlspecialchars((string) ($comment['last_reported_at'] ?? '')); ?></span>
                    </div>
                </div>
                <div class="intel-item-actions">
                    <a href="/posts/show?id=<?= (int) $comment['post_id']; ?>" class="btn btn-sm btn-outline-info">Inspect</a>
                </div>
            </article>
        <?php endforeach; ?>
    <?php endif; ?>
</div>