<?php use Intranet\Core\Helpers; ?>
<div class="row justify-content-center">
    <div class="col-md-5">
        <div class="glass-panel p-4">
            <h1 class="h3 fw-bold mb-3">Create Analyst Account</h1>
            <?php if (!empty($error)): ?><div class="alert alert-danger"><?= Helpers::e($error) ?></div><?php endif; ?>
            <form method="post" action="/register" class="vstack gap-3">
                <input type="hidden" name="_csrf" value="<?= Helpers::e($csrf) ?>">
                <input class="form-control" type="email" name="email" placeholder="Email" required>
                <input class="form-control" type="text" name="display_name" placeholder="Display name" required>
                <input class="form-control" type="password" name="password" placeholder="Password (8+ chars)" required>
                <button class="btn btn-primary" type="submit">Create account</button>
            </form>
        </div>
    </div>
</div>
