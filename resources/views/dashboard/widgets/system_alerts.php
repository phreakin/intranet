<?php

use Intranet\Core\Helpers;

$alerts = $alerts ?? [];
?>
<div class="intel-alert-list">
    <?php foreach ($alerts as $alert): ?>
        <article class="intel-alert-card">
            <span class="alert-severity severity-<?= Helpers::e((string) ($alert['severity'] ?? 'low')) ?>"></span>
            <div class="flex-grow-1">
                <div class="intel-feed-meta"><?= strtoupper(Helpers::e((string) ($alert['severity'] ?? 'low'))) ?> PRIORITY</div>
                <div class="intel-feed-body"><strong><?= Helpers::e((string) ($alert['title'] ?? 'Alert')) ?></strong></div>
                <div class="panel-copy mt-1"><?= Helpers::e((string) ($alert['message'] ?? 'No details available.')) ?></div>
            </div>
        </article>
    <?php endforeach; ?>
</div>
