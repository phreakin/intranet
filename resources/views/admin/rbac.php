<?php use Intranet\Core\Helpers; ?>
<div class="glass-panel p-4">
    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
        <div>
            <h1 class="h4 mb-1">Role &amp; Permission Matrix</h1>
            <p class="text-secondary small mb-0">Fine-grained permissions are checked via <code>Auth::can()</code>. Admins bypass this matrix automatically.</p>
        </div>
        <a class="btn btn-sm btn-outline-light" href="/admin">Back to Admin</a>
    </div>

    <?php foreach (($roles ?? []) as $role): ?>
        <?php $grantedSet = $matrix[(int) $role['id']] ?? []; ?>
        <form method="post" action="/admin/rbac/<?= (int) $role['id'] ?>" class="glass-panel p-3 mb-3">
            <input type="hidden" name="_csrf" value="<?= Helpers::e($csrf) ?>">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h2 class="h6 mb-0"><?= Helpers::e($role['name']) ?></h2>
                <button class="btn btn-sm btn-primary" type="submit">Save <?= Helpers::e($role['name']) ?> grants</button>
            </div>
            <div class="row g-2">
                <?php foreach (($permissions ?? []) as $perm): ?>
                    <?php $checked = isset($grantedSet[(int) $perm['id']]); ?>
                    <div class="col-md-4 col-lg-3">
                        <label class="d-flex align-items-center gap-2 small">
                            <input type="checkbox"
                                   class="form-check-input m-0"
                                   name="permission_ids[]"
                                   value="<?= (int) $perm['id'] ?>"
                                   <?= $checked ? 'checked' : '' ?>
                                   <?= strtolower((string) $role['name']) === 'admin' ? 'disabled' : '' ?>>
                            <span class="text-light"><?= Helpers::e($perm['name']) ?></span>
                        </label>
                    </div>
                <?php endforeach; ?>
            </div>
            <?php if (strtolower((string) $role['name']) === 'admin'): ?>
                <div class="small text-secondary mt-2">Admins always pass all permission checks — this matrix is informational for this role.</div>
            <?php endif; ?>
        </form>
    <?php endforeach; ?>
</div>
