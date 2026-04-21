<?php use Intranet\Core\Helpers; ?>
<div class="page-shell">
    <section class="page-hero glass-panel">
        <div class="page-hero-grid">
            <div>
                <span class="eyebrow">Edit Record</span>
                <h1 class="page-title">Refine title, metadata, and taxonomy without leaving the dark operations language.</h1>
                <p class="page-copy">Editing uses the same compact intake structure as submission, so the tool feels unified instead of patched together.</p>
            </div>
            <div class="page-meta">
                <span class="chip chip-category"><?= !empty($isAdminEdit) ? 'Admin Edit' : 'Owner Edit' ?></span>
                <span class="chip chip-status">Structured Form</span>
            </div>
        </div>
    </section>

    <section class="intel-form-panel glass-panel mx-auto" style="max-width: 920px;">
        <div class="intel-panel-header">
            <div>
                <div class="panel-kicker">Editable Fields</div>
                <h2 class="panel-title"><?= !empty($isAdminEdit) ? 'Administrative post edit' : 'Post edit' ?></h2>
                <p class="panel-copy">Tight spacing, dark inputs, and immediate action focus.</p>
            </div>
        </div>

        <form method="post" action="<?= !empty($isAdminEdit) ? '/admin/posts/' . (int) $post['id'] . '/edit' : '/post/' . (int) $post['id'] . '/edit' ?>" class="row g-3">
            <input type="hidden" name="_csrf" value="<?= Helpers::e($csrf) ?>">

            <div class="col-12">
                <label class="form-label" for="edit-title">Title</label>
                <input id="edit-title" type="text" class="form-control" name="title" value="<?= Helpers::e($post['title']) ?>" required>
            </div>

            <div class="col-12">
                <label class="form-label" for="edit-description">Description</label>
                <textarea id="edit-description" class="form-control" name="description" rows="5"><?= Helpers::e($post['description']) ?></textarea>
            </div>

            <div class="col-md-6">
                <label class="form-label" for="edit-category">Category</label>
                <select id="edit-category" class="form-select" name="category_id">
                    <option value="">Uncategorized</option>
                    <?php foreach (($categories ?? []) as $category): ?>
                        <option value="<?= (int) $category['id'] ?>" <?= (int) $post['category_id'] === (int) $category['id'] ? 'selected' : '' ?>><?= Helpers::e($category['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-6">
                <label class="form-label" for="edit-tags">Tags</label>
                <input id="edit-tags" type="text" class="form-control" name="tags" value="<?= Helpers::e((string) $post['tags']) ?>" placeholder="tag1, tag2">
            </div>

            <div class="col-12">
                <button class="btn btn-primary" type="submit">Save Changes</button>
            </div>
        </form>
    </section>
</div>
