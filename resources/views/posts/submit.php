<?php use Intranet\Core\Helpers; ?>
<div class="page-shell">
    <section class="page-hero glass-panel">
        <div class="page-hero-grid">
            <div>
                <span class="eyebrow">Submission</span>
                <h1 class="page-title">Ingest a new source with metadata preview, compact editing, and classification controls.</h1>
                <p class="page-copy">The form stays lightweight and server-rendered, but it still feels like an evidence intake console rather than a generic CMS form.</p>
            </div>
            <div class="page-meta">
                <span class="chip chip-status">Metadata Fetch</span>
                <span class="chip chip-category">Compact Form</span>
                <span class="chip chip-tag">Auto-fill Ready</span>
            </div>
        </div>
    </section>

    <div class="row g-3">
        <div class="col-12 col-xl-8">
            <section class="intel-form-panel glass-panel">
                <div class="intel-panel-header">
                    <div>
                        <div class="panel-kicker">Source Intake</div>
                        <h2 class="panel-title">Primary URL capture</h2>
                        <p class="panel-copy">Fetch metadata first, then correct anything the extractor got wrong.</p>
                    </div>
                </div>

                <?php if (!empty($error)): ?><div class="alert alert-danger mb-3"><?= Helpers::e($error) ?></div><?php endif; ?>
                <?php if (!empty($metaError)): ?><div class="alert alert-warning mb-3"><?= Helpers::e($metaError) ?></div><?php endif; ?>

                <form method="get" action="/submit" class="row g-2 mb-4">
                    <div class="col-md-9">
                        <label class="form-label" for="source-url-fetch">Source URL</label>
                        <input id="source-url-fetch" class="form-control" type="url" name="url" placeholder="https://example.com/article" value="<?= Helpers::e((string) (($prefill['url'] ?? ''))) ?>" required>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-outline-info w-100">Fetch Metadata</button>
                    </div>
                </form>

                <form method="post" action="/submit" class="row g-3">
                    <input type="hidden" name="_csrf" value="<?= Helpers::e($csrf) ?>">

                    <div class="col-12">
                        <label class="form-label" for="source-url">Source URL</label>
                        <input id="source-url" class="form-control" type="url" name="url" placeholder="https://example.com/article" value="<?= Helpers::e((string) (($prefill['url'] ?? ''))) ?>" required>
                    </div>

                    <div class="col-md-8">
                        <label class="form-label" for="post-title">Title</label>
                        <input id="post-title" class="form-control" type="text" name="title" value="<?= Helpers::e((string) (($prefill['title'] ?? ''))) ?>" required>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label" for="publish-date">Publish Date</label>
                        <input id="publish-date" class="form-control" type="text" name="published_at" value="<?= Helpers::e((string) (($prefill['published_at'] ?? ''))) ?>" placeholder="2026-01-31 09:15:00">
                    </div>

                    <div class="col-12">
                        <label class="form-label" for="description">Description</label>
                        <textarea id="description" class="form-control" name="description" rows="4"><?= Helpers::e((string) (($prefill['description'] ?? ''))) ?></textarea>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label" for="thumb-url">Thumbnail URL</label>
                        <input id="thumb-url" class="form-control" type="url" name="thumbnail_url" value="<?= Helpers::e((string) (($prefill['thumbnail_url'] ?? ''))) ?>">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label" for="canonical-url">Canonical URL</label>
                        <input id="canonical-url" class="form-control" type="url" name="canonical_url" value="<?= Helpers::e((string) (($prefill['canonical_url'] ?? ''))) ?>">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label" for="site-name">Site Name</label>
                        <input id="site-name" class="form-control" type="text" name="site_name" value="<?= Helpers::e((string) (($prefill['site_name'] ?? ''))) ?>">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label" for="author-name">Author</label>
                        <input id="author-name" class="form-control" type="text" name="author_name" value="<?= Helpers::e((string) (($prefill['author_name'] ?? ''))) ?>">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label" for="category-id">Category</label>
                        <select id="category-id" class="form-select" name="category_id">
                            <option value="">Uncategorized</option>
                            <?php foreach (($categories ?? []) as $category): ?>
                                <option value="<?= (int) $category['id'] ?>"><?= Helpers::e($category['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label" for="new-category">Create Category</label>
                        <input id="new-category" class="form-control" type="text" name="new_category" placeholder="Incident response">
                    </div>

                    <div class="col-12">
                        <label class="form-label" for="tags">Tags</label>
                        <input id="tags" class="form-control" type="text" name="tags" value="<?= Helpers::e((string) (($prefill['tags'] ?? ''))) ?>" placeholder="security, osint, reference">
                        <div class="form-text">Manual tags override missing source keywords and keep retrieval quality high.</div>
                    </div>

                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">Ingest Link</button>
                    </div>
                </form>
            </section>
        </div>

        <div class="col-12 col-xl-4">
            <section class="glass-panel section-card h-100">
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
