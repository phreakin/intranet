<?php use Intranet\Core\Helpers; ?>

<div class="row justify-content-center py-5">
    <div class="col-md-5">

        <div class="glass-panel p-4 rounded-4 shadow-lg"
             style="backdrop-filter: blur(14px); background: rgba(20,20,30,0.55); border: 1px solid rgba(255,255,255,0.08);">

            <h1 class="h3 fw-bold mb-4 text-center text-light">Login</h1>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger"><?= Helpers::e($error) ?></div>
            <?php endif; ?>

            <form method="post" action="/login" class="vstack gap-3">
                <input type="hidden" name="_csrf" value="<?= Helpers::e($csrf) ?>">

                <div class="form-floating">
                    <input class="form-control bg-dark text-light border-secondary"
                           type="email" name="email" id="emailInput" placeholder="Email" required>
                    <label for="emailInput" class="text-secondary">Email</label>
                </div>

                <div class="form-floating">
                    <input class="form-control bg-dark text-light border-secondary"
                           type="password" name="password" id="passwordInput" placeholder="Password" required>
                    <label for="passwordInput" class="text-secondary">Password</label>
                </div>

                <button class="btn btn-primary w-100 py-2 fw-semibold" type="submit">
                    <img src="<?= Helpers::svgsNav()['users'] ?>" width="20">
                    Sign In
                </button>
            </form>

            <hr class="border-secondary my-4">

            <div class="text-center text-secondary mb-2">Or Login with</div>

            <div class="d-flex flex-column gap-2">

                <?php if (!empty($providers['google']['enabled'])): ?>
                    <a href="/auth/oauth/google"
                       class="btn d-flex align-items-center justify-content-center gap-2 fw-semibold"
                       style="background:#DB4437; color:#fff;">
                        <img src="<?= Helpers::svgsColor()['google'] ?>" width="32" alt="Google">
                        Continue with Google
                    </a>
                <?php endif; ?>

                <?php if (!empty($providers['github']['enabled'])): ?>
                    <a href="/auth/oauth/github"
                       class="btn d-flex align-items-center justify-content-center gap-2 fw-semibold"
                       style="background:#000; color:#fff;">
                        <img src="<?= Helpers::svgsColor()['github'] ?>" width="32" alt="GitHub">
                        Continue with GitHub
                    </a>
                <?php endif; ?>

                <?php if (!empty($providers['reddit']['enabled'])): ?>
                    <a href="/auth/oauth/reddit"
                       class="btn d-flex align-items-center justify-content-center gap-2 fw-semibold"
                       style="background:#FF4500; color:#fff;">
                        <img src="<?= Helpers::svgsColor()['reddit'] ?>" width="32" alt="Reddit">
                        Continue with Reddit
                    </a>
                <?php endif; ?>

            </div>

        </div>
    </div>
</div>
