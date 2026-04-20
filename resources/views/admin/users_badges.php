<?php use Intranet\Core\Helpers; ?>
<div class="glass-panel p-4">
    <h1 class="h4">User & Badge Management</h1>
    <div class="table-responsive">
        <table class="table table-dark table-striped align-middle">
            <thead><tr><th>User</th><th>Email</th><th>Roles</th><th>Badges</th><th>Actions</th></tr></thead>
            <tbody>
            <?php foreach (($users ?? []) as $user): ?>
                <tr>
                    <td><a href="/user/<?= (int) $user['id'] ?>" class="text-light"><?= Helpers::e($user['display_name']) ?></a></td>
                    <td><?= Helpers::e($user['email']) ?></td>
                    <td><?= Helpers::e((string) $user['roles']) ?></td>
                    <td><?= Helpers::e((string) $user['badges']) ?></td>
                    <td>
                        <form method="post" action="/admin/users/<?= (int) $user['id'] ?>/badge" class="d-flex gap-1 mb-1">
                            <input type="hidden" name="_csrf" value="<?= Helpers::e($csrf) ?>">
                            <select name="badge_id" class="form-select form-select-sm">
                                <?php foreach (($badges ?? []) as $badge): ?>
                                    <option value="<?= (int) $badge['id'] ?>"><?= Helpers::e($badge['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <button class="btn btn-sm btn-outline-info" type="submit">Assign badge</button>
                        </form>
                        <form method="post" action="/admin/users/<?= (int) $user['id'] ?>/role" class="d-flex gap-1">
                            <input type="hidden" name="_csrf" value="<?= Helpers::e($csrf) ?>">
                            <select name="role_id" class="form-select form-select-sm">
                                <?php foreach (($roles ?? []) as $role): ?>
                                    <option value="<?= (int) $role['id'] ?>"><?= Helpers::e($role['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <button class="btn btn-sm btn-outline-warning" type="submit">Assign role</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
