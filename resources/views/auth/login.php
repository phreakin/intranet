<?php use Intranet\Core\Helpers; ?>
<div class="row justify-content-center">
    <div class="col-md-5">
        <div class="glass-panel p-4">
            <h1 class="h3 fw-bold mb-3">Authentication Gateway</h1>
            <?php if (!empty($error)): ?><div class="alert alert-danger"><?= Helpers::e($error) ?></div><?php endif; ?>
            <form method="post" action="/login" class="vstack gap-3">
                <input type="hidden" name="_csrf" value="<?= Helpers::e($csrf) ?>">
                <input class="form-control" type="email" name="email" placeholder="Email" required>
                <input class="form-control" type="password" name="password" placeholder="Password" required>
                <button class="btn btn-primary" type="submit">Sign in</button>
            </form>
            <hr class="border-secondary">
            <div class="d-flex flex-wrap gap-2">
                <?php foreach (['google' => 'Google', 'facebook' => 'Facebook', 'github' => 'GitHub'] as $key => $name): ?>
                    <?php if (!empty($providers[$key]['enabled'])): ?>
                        <a href="/auth/oauth/<?= $key ?>" class="btn btn-outline-light btn-sm">Continue with <?= $name ?></a>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>
