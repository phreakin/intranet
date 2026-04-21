<?php

use Intranet\Core\Helpers;

$alerts = $alerts ?? [];
?>
<div class="flex flex-col gap-3">
    <?php foreach ($alerts as $alert): ?>
        <?php
        $severity = strtolower((string) ($alert['severity'] ?? 'low'));
        $colorMap = [
            'low' => 'border-slate-500/30 bg-slate-500/10',
            'medium' => 'border-yellow-500/40 bg-yellow-500/10',
            'high' => 'border-red-500/40 bg-red-500/10',
        ];
        ?>

        <article class="flex gap-3 rounded-xl border <?= $colorMap[$severity] ?? $colorMap['low'] ?> p-3">

            <div class="w-2 rounded-full bg-current opacity-70"></div>

            <div class="flex-1">
                <div class="text-[10px] uppercase tracking-wider text-slate-400">
                    <?= strtoupper(Helpers::e($severity)) ?> PRIORITY
                </div>

                <div class="text-sm font-semibold text-white mt-1">
                    <?= Helpers::e((string) ($alert['title'] ?? 'Alert')) ?>
                </div>

                <div class="text-xs text-slate-300 mt-1">
                    <?= Helpers::e((string) ($alert['message'] ?? 'No details available.')) ?>
                </div>
            </div>

        </article>
    <?php endforeach; ?>
</div>
