<?php

use Intranet\Core\Helpers;

$items = $items ?? [];
?>
<div class="flex flex-col gap-3">
    <?php foreach ($items as $item): ?>
        <article class="rounded-xl border border-white/10 bg-white/5 p-3">

            <div class="text-[10px] uppercase tracking-wider text-slate-500">
                <?= Helpers::e(strtoupper((string) ($item['activity_type'] ?? 'activity'))) ?>
            </div>

            <div class="text-sm font-semibold text-white mt-1">
                <?= Helpers::e((string) ($item['title'] ?? 'Untitled activity')) ?>
            </div>

            <div class="text-xs text-slate-400 mt-1">
                <?= Helpers::e((string) ($item['activity_label'] ?? 'Activity recorded.')) ?>
            </div>

        </article>
    <?php endforeach; ?>
</div>
