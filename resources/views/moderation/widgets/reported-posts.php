<div class="intel-list">
    <?php if (empty($posts)): ?>
        <div class="empty-state">No reported posts found.</div>
    <?php else: ?>
        <?php foreach ($posts as $post): ?>
            <article class="intel-item">
                <div class="intel-item-main">
                    <h3 class="intel-item-title">
                        <a href="/posts/show?id=<?= (int) $post['id']; ?>">
                            <?= htmlspecialchars($post['title'] ?? 'Untitled'); ?>
                        </a>
                    </h3>
                    <p class="intel-item-text">
                        <?= htmlspecialchars(mb_strimwidth((string) ($post['description'] ?? ''), 0, 160, '...')); ?>
                    </p>
                    <div class="intel-meta-row">
                        <span class="status-chip reported">Reported</span>
                        <span class="telemetry-pill">Reports: <?= (int) ($post['report_count'] ?? 0); ?></span>
                        <span class="telemetry-pill">Last: <?= htmlspecialchars((string) ($post['last_reported_at'] ?? '')); ?></span>
                    </div>
                </div>
                <div class="intel-item-actions">
                    <a href="/posts/show?id=<?= (int) $post['id']; ?>" class="btn btn-sm btn-outline-info">Inspect</a>
                </div>
            </article>
        <?php endforeach; ?>
    <?php endif; ?>
</div>