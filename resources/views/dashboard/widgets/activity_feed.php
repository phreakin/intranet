<?php

use Intranet\Core\Helpers;

$items = $items ?? [];
?>
<div class="intel-feed-list">
    <?php foreach ($items as $item): ?>
        <article class="intel-feed-item">
            <div class="intel-feed-meta"><?= Helpers::e(strtoupper((string) ($item['activity_type'] ?? 'activity'))) ?></div>
            <div class="intel-feed-body"><strong><?= Helpers::e((string) ($item['title'] ?? 'Untitled activity')) ?></strong></div>
            <div class="panel-copy mt-1"><?= Helpers::e((string) ($item['activity_label'] ?? 'Activity recorded.')) ?></div>
        </article>
    <?php endforeach; ?>
</div>
