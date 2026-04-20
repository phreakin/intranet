<?php use Intranet\Core\Helpers; ?>
<div class="glass-panel p-4">
    <h1 class="h4">Reported Content</h1>
    <div class="row g-3">
        <div class="col-lg-6">
            <h2 class="h6 text-uppercase text-secondary">Post reports</h2>
            <ul class="list-group list-group-flush">
                <?php foreach (($queue['post_reports'] ?? []) as $report): ?>
                    <li class="list-group-item bg-transparent border-secondary">
                        <div class="small">Post #<?= (int) $report['post_id'] ?> · <?= Helpers::e($report['title']) ?></div>
                        <div class="text-secondary small">Reason: <?= Helpers::e($report['reason']) ?></div>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
        <div class="col-lg-6">
            <h2 class="h6 text-uppercase text-secondary">Comment reports</h2>
            <ul class="list-group list-group-flush">
                <?php foreach (($queue['comment_reports'] ?? []) as $report): ?>
                    <li class="list-group-item bg-transparent border-secondary">
                        <div class="small">Comment #<?= (int) $report['comment_id'] ?></div>
                        <div class="text-secondary small">Reason: <?= Helpers::e($report['reason']) ?></div>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</div>
