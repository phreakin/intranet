<?php use Intranet\Core\Helpers; ?>
<div class="glass-panel p-4 col-12 col-xl-8 mx-auto text-center">
    <h1 class="h3 mb-2"><?= Helpers::e($title ?? 'Not found') ?></h1>
    <p class="text-secondary mb-3"><?= Helpers::e($message ?? 'The resource could not be located.') ?></p>
    <a class="btn btn-outline-light btn-sm" href="/">Return to the feed</a>
</div>
