<div class="flex flex-col gap-3">
    <?php if (empty($posts)): ?>
        <div class="empty-state">No reported posts found.</div>
    <?php else: ?>
        <?php foreach ($posts as $post): ?>
            <?php
            $confidence = (int) ($post['ai_confidence'] ?? $post['confidence_score'] ?? min(98, 52 + ((int) ($post['report_count'] ?? 0) * 9)));
            $confidenceTone = $confidence >= 85
                ? 'border-red-500/40 bg-red-500/10 text-red-100'
                : ($confidence >= 65
                    ? 'border-yellow-500/40 bg-yellow-500/10 text-yellow-100'
                    : 'border-cyan-500/30 bg-cyan-500/10 text-cyan-100');
            $confidenceBar = $confidence >= 85
                ? 'bg-red-400'
                : ($confidence >= 65 ? 'bg-yellow-400' : 'bg-cyan-400');
            ?>
            <article class="rounded-xl border border-white/10 bg-white/5 p-4 transition hover:border-cyan-400/30 hover:bg-cyan-400/5">
                <div class="grid grid-cols-1 gap-4 xl:grid-cols-12">
                    <div class="xl:col-span-7">
                        <h3 class="text-sm font-semibold text-white">
                            <a class="transition hover:text-cyan-200" href="/posts/show?id=<?= (int) $post['id']; ?>">
                                <?= htmlspecialchars($post['title'] ?? 'Untitled'); ?>
                            </a>
                        </h3>
                        <p class="mt-2 text-sm leading-6 text-slate-400">
                            <?= htmlspecialchars(mb_strimwidth((string) ($post['description'] ?? ''), 0, 160, '...')); ?>
                        </p>
                        <div class="mt-3 flex flex-wrap gap-2">
                            <span class="status-chip reported">Reported</span>
                            <span class="telemetry-pill">Reports: <?= (int) ($post['report_count'] ?? 0); ?></span>
                            <span class="telemetry-pill">Last: <?= htmlspecialchars((string) ($post['last_reported_at'] ?? '')); ?></span>
                        </div>
                    </div>

                    <div class="xl:col-span-3">
                        <div class="rounded-xl border <?= $confidenceTone ?> p-3">
                            <div class="flex items-center justify-between gap-3">
                                <span class="text-[10px] font-semibold uppercase tracking-[0.2em] opacity-80">AI Confidence</span>
                                <span class="text-sm font-bold"><?= $confidence ?>%</span>
                            </div>
                            <div class="mt-3 h-1.5 rounded-full bg-black/30">
                                <div class="h-1.5 rounded-full <?= $confidenceBar ?>" style="width: <?= $confidence ?>%"></div>
                            </div>
                            <div class="mt-2 text-xs opacity-80">Automated risk signal estimate</div>
                        </div>
                    </div>

                    <div class="flex items-center xl:col-span-2 xl:justify-end">
                        <a href="/posts/show?id=<?= (int) $post['id']; ?>" class="inline-flex w-full items-center justify-center rounded-lg border border-cyan-400/30 bg-cyan-400/10 px-3 py-2 text-sm font-medium text-cyan-100 transition hover:bg-cyan-400/20 xl:w-auto">Inspect</a>
                    </div>
                </div>
            </article>
        <?php endforeach; ?>
    <?php endif; ?>
</div>