<?php use Intranet\Core\Helpers; ?>
<div class="glass-panel p-4">
    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-4">
        <div>
            <h1 class="h4 mb-1">CMS Menus</h1>
            <p class="text-secondary small mb-0">Define lightweight navigation menus for public CMS surfaces.</p>
        </div>
        <a class="btn btn-sm btn-outline-light" href="/admin/cms/pages">Back to CMS Pages</a>
    </div>

    <form method="post" action="/admin/cms/menus" class="glass-panel p-3 mb-4">
        <input type="hidden" name="_csrf" value="<?= Helpers::e($csrf) ?>">
        <div class="row g-3">
            <div class="col-md-5">
                <label class="form-label small text-secondary">Slug</label>
                <input class="form-control" type="text" name="slug" placeholder="primary" required>
            </div>
            <div class="col-md-5">
                <label class="form-label small text-secondary">Label</label>
                <input class="form-control" type="text" name="label" placeholder="Primary Navigation" required>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button class="btn btn-primary w-100" type="submit">Create</button>
            </div>
        </div>
    </form>

    <?php foreach (($menus ?? []) as $menu): ?>
        <?php $menuItems = $items[(int) $menu['id']] ?? []; ?>
        <section class="glass-panel p-3 mb-3">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                <div>
                    <h2 class="h6 mb-1"><?= Helpers::e((string) $menu['label']) ?></h2>
                    <div class="small text-secondary">Slug: <code><?= Helpers::e((string) $menu['slug']) ?></code></div>
                </div>
                <form method="post" action="/admin/cms/menus/<?= (int) $menu['id'] ?>/delete" onsubmit="return confirm('Delete this menu and its items?');">
                    <input type="hidden" name="_csrf" value="<?= Helpers::e($csrf) ?>">
                    <button class="btn btn-sm btn-outline-danger" type="submit">Delete Menu</button>
                </form>
            </div>

            <form method="post" action="/admin/cms/menus/<?= (int) $menu['id'] ?>/items" class="row g-3 mb-3">
                <input type="hidden" name="_csrf" value="<?= Helpers::e($csrf) ?>">
                <div class="col-md-3">
                    <input class="form-control" type="text" name="label" placeholder="Label" required>
                </div>
                <div class="col-md-4">
                    <input class="form-control" type="text" name="url" placeholder="/pages/about" required>
                </div>
                <div class="col-md-2">
                    <input class="form-control" type="number" name="position" value="0" min="0">
                </div>
                <div class="col-md-2">
                    <select class="form-select" name="target">
                        <option value="_self">Same Tab</option>
                        <option value="_blank">New Tab</option>
                    </select>
                </div>
                <div class="col-md-1 d-flex align-items-center">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1" checked>
                    </div>
                </div>
                <div class="col-12 d-flex justify-content-end">
                    <button class="btn btn-outline-info btn-sm" type="submit">Add Menu Item</button>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-sm align-middle">
                    <thead>
                        <tr>
                            <th>Label</th>
                            <th>URL</th>
                            <th>Position</th>
                            <th>Target</th>
                            <th>Active</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($menuItems as $item): ?>
                            <tr>
                                <td><?= Helpers::e((string) $item['label']) ?></td>
                                <td><code><?= Helpers::e((string) $item['url']) ?></code></td>
                                <td><?= (int) $item['position'] ?></td>
                                <td><?= Helpers::e((string) $item['target']) ?></td>
                                <td><?= !empty($item['is_active']) ? 'Yes' : 'No' ?></td>
                                <td class="text-end">
                                    <form method="post" action="/admin/cms/menu-items/<?= (int) $item['id'] ?>/delete" onsubmit="return confirm('Delete this menu item?');">
                                        <input type="hidden" name="_csrf" value="<?= Helpers::e($csrf) ?>">
                                        <button class="btn btn-sm btn-outline-danger" type="submit">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>
    <?php endforeach; ?>
</div>
