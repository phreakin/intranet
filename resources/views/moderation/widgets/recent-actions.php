<div class="feed-stream">
    <?php if (empty($actions)): ?>
        <div class="empty-state">No moderation log data found.</div>
    <?php else: ?>
        <?php foreach ($actions as $action): ?>
            <div class="feed-event">
                <div class="feed-dot danger"></div>
                <div class="feed-content">
                    <div class="feed-title">
                        <?= htmlspecialchars((string) ($action['action_type'] ?? 'action')); ?>
                    </div>
                    <div class="feed-text">
                        Target: <?= htmlspecialchars((string) ($action['target_type'] ?? 'unknown')); ?>
                        #<?= (int) ($action['target_id'] ?? 0); ?>
                    </div>
                    <div class="feed-text">
                        <?= htmlspecialchars((string) ($action['reason'] ?? 'No reason recorded')); ?>
                    </div>
                    <div class="feed-time">
                        <?= htmlspecialchars((string) ($action['created_at'] ?? '')); ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>