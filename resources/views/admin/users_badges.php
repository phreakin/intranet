<?php use Intranet\Core\Helpers; ?>
<div class="glass-panel p-4">
    <h1 class="h4">User & Badge Management</h1>
    <div class="table-responsive">
        <table class="table table-dark table-striped align-middle">
            <thead><tr><th>User</th><th>Email</th><th>Roles</th><th>Badges</th></tr></thead>
            <tbody>
            <?php foreach (($users ?? []) as $user): ?>
                <tr>
                    <td><a href="/user/<?= (int) $user['id'] ?>" class="text-light"><?= Helpers::e($user['display_name']) ?></a></td>
                    <td><?= Helpers::e($user['email']) ?></td>
                    <td><?= Helpers::e((string) $user['roles']) ?></td>
                    <td><?= Helpers::e((string) $user['badges']) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
