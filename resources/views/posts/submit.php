<?php use Intranet\Core\Helpers; ?>
<div class="page-shell">
    <section class="page-hero glass-panel">
        <div class="page-hero-grid">
            <div>
                <span class="eyebrow">Submission</span>
                <h1 class="page-title">Ingest a new source with metadata preview, compact editing, and classification controls.</h1>
                <p class="page-copy">The form stays lightweight and server-rendered, but it still feels like an evidence intake console rather than a generic CMS form.</p>
            </div>
            <div class="page-meta flex flex-wrap gap-2">
                <span class="chip chip-status">Metadata Fetch</span>
                <span class="chip chip-category">Compact Form</span>
                <span class="chip chip-tag">Auto-fill Ready</span>
            </div>
        </div>
    </section>

    <div class="grid grid-cols-1 gap-4 xl:grid-cols-3">
        <div class="xl:col-span-2">
            <section class="intel-form-panel glass-panel">
                <div class="intel-panel-header">
                    <div>
                        <div class="panel-kicker">Source Intake</div>
                        <h2 class="panel-title">Primary URL capture</h2>
                        <p class="panel-copy">Fetch metadata first, then correct anything the extractor got wrong.</p>
                    </div>
                </div>

                <?php if (!empty($error)): ?><div class="mb-3 rounded-xl border border-red-500/30 bg-red-500/10 px-4 py-3 text-sm text-red-100"><?= Helpers::e($error) ?></div><?php endif; ?>
                <?php if (!empty($metaError)): ?><div class="mb-3 rounded-xl border border-yellow-500/30 bg-yellow-500/10 px-4 py-3 text-sm text-yellow-100"><?= Helpers::e($metaError) ?></div><?php endif; ?>

                <form method="get" action="/submit" class="mb-4 grid grid-cols-1 gap-3 md:grid-cols-12">
                    <div class="md:col-span-9">
                        <label class="mb-2 block text-sm font-medium text-slate-200" for="source-url-fetch">Source URL</label>
                        <input id="source-url-fetch" class="search-input" type="url" name="url" placeholder="https://example.com/article" value="<?= Helpers::e((string) (($prefill['url'] ?? ''))) ?>" required>
                    </div>
                    <div class="md:col-span-3 md:self-end">
                        <button type="submit" class="inline-flex w-full items-center justify-center rounded-lg border border-cyan-400/30 bg-cyan-400/10 px-4 py-2 text-sm font-medium text-cyan-100 transition hover:bg-cyan-400/20">Fetch Metadata</button>
                    </div>
                </form>

                <form method="post" action="/submit" class="grid grid-cols-1 gap-4 md:grid-cols-12">
                    <input type="hidden" name="_csrf" value="<?= Helpers::e($csrf) ?>">

                    <div class="md:col-span-12">
                        <label class="mb-2 block text-sm font-medium text-slate-200" for="source-url">Source URL</label>
                        <input id="source-url" class="search-input" type="url" name="url" placeholder="https://example.com/article" value="<?= Helpers::e((string) (($prefill['url'] ?? ''))) ?>" required>
                    </div>

                    <div class="md:col-span-8">
                        <label class="mb-2 block text-sm font-medium text-slate-200" for="post-title">Title</label>
                        <input id="post-title" class="search-input" type="text" name="title" value="<?= Helpers::e((string) (($prefill['title'] ?? ''))) ?>" required>
                    </div>

                    <div class="md:col-span-4">
                        <label class="mb-2 block text-sm font-medium text-slate-200" for="publish-date">Publish Date</label>
                        <input id="publish-date" class="search-input" type="text" name="published_at" value="<?= Helpers::e((string) (($prefill['published_at'] ?? ''))) ?>" placeholder="2026-01-31 09:15:00">
                    </div>

                    <div class="md:col-span-12">
                        <label class="mb-2 block text-sm font-medium text-slate-200" for="description">Description</label>
                        <textarea id="description" class="search-input min-h-[120px]" name="description" rows="4"><?= Helpers::e((string) (($prefill['description'] ?? ''))) ?></textarea>
                    </div>

                    <div class="md:col-span-6">
                        <label class="mb-2 block text-sm font-medium text-slate-200" for="thumb-url">Thumbnail URL</label>
                        <input id="thumb-url" class="search-input" type="url" name="thumbnail_url" value="<?= Helpers::e((string) (($prefill['thumbnail_url'] ?? ''))) ?>">
                    </div>

                    <div class="md:col-span-6">
                        <label class="mb-2 block text-sm font-medium text-slate-200" for="canonical-url">Canonical URL</label>
                        <input id="canonical-url" class="search-input" type="url" name="canonical_url" value="<?= Helpers::e((string) (($prefill['canonical_url'] ?? ''))) ?>">
                    </div>

                    <div class="md:col-span-6">
                        <label class="mb-2 block text-sm font-medium text-slate-200" for="site-name">Site Name</label>
                        <input id="site-name" class="search-input" type="text" name="site_name" value="<?= Helpers::e((string) (($prefill['site_name'] ?? ''))) ?>">
                    </div>

                    <div class="md:col-span-6">
                        <label class="mb-2 block text-sm font-medium text-slate-200" for="author-name">Author</label>
                        <input id="author-name" class="search-input" type="text" name="author_name" value="<?= Helpers::e((string) (($prefill['author_name'] ?? ''))) ?>">
                    </div>

                    <div class="md:col-span-6">
                        <label class="mb-2 block text-sm font-medium text-slate-200" for="category-id">Category</label>
                        <select id="category-id" class="search-input" name="category_id">
                            <option value="">Uncategorized</option>
                            <?php foreach (($categories ?? []) as $category): ?>
                                <option value="<?= (int) $category['id'] ?>"><?= Helpers::e($category['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="md:col-span-6">
                        <label class="mb-2 block text-sm font-medium text-slate-200" for="new-category">Create Category</label>
                        <input id="new-category" class="search-input" type="text" name="new_category" placeholder="Incident response">
                    </div>

                    <div class="md:col-span-12">
                        <label class="mb-2 block text-sm font-medium text-slate-200" for="tags">Tags</label>
                        <input id="tags" class="search-input" type="text" name="tags" value="<?= Helpers::e((string) (($prefill['tags'] ?? ''))) ?>" placeholder="security, osint, reference">
                        <div class="mt-2 text-xs text-slate-400">Manual tags override missing source keywords and keep retrieval quality high.</div>
                    </div>

                    <div class="md:col-span-12">
                        <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-cyan-500 px-4 py-2 text-sm font-semibold text-slate-950 transition hover:bg-cyan-400">Ingest Link</button>
                    </div>
                </form>
            </section>
        </div>

        <div class="xl:col-span-1">
            <section class="glass-panel section-card h-full">
                <div class="panel-kicker">Preview</div>
                <h2 class="panel-title">Metadata extraction snapshot</h2>
                <p class="panel-copy">This acts like a small forensic side panel for operators validating imported fields.</p>

                <div class="info-list mt-4">
                    <div class="info-row">
                        <div class="info-label">URL</div>
                        <div class="info-value"><?= Helpers::e((string) (($prefill['url'] ?? 'Not loaded yet'))) ?></div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Title</div>
                        <div class="info-value"><?= Helpers::e((string) (($prefill['title'] ?? 'Awaiting metadata'))) ?></div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Site</div>
                        <div class="info-value"><?= Helpers::e((string) (($prefill['site_name'] ?? 'Unknown'))) ?></div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Author</div>
                        <div class="info-value"><?= Helpers::e((string) (($prefill['author_name'] ?? 'Unknown'))) ?></div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Tags</div>
                        <div class="info-value"><?= Helpers::e((string) (($prefill['tags'] ?? 'No tags yet'))) ?></div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>
