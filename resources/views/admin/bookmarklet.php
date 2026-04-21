<?php use Intranet\Core\Helpers; ?>
<div class="glass-panel p-4">
    <h1 class="h4">Admin Bookmarklet</h1>
    <p class="text-secondary">Drag this to your bookmarks bar, then click on any page to prefill the intranet submit form.</p>
    <?php
    $baseUrl = rtrim((string) (getenv('APP_URL') ?: 'http://localhost:8080'), '/');
    $bookmarklet = "javascript:(function(){var m=document.querySelector('meta[name=description],meta[property=\"og:description\"]');var i=document.querySelector('meta[property=\"og:image\"],meta[name=\"twitter:image\"]');var k=document.querySelector('meta[name=keywords]');var q='url='+encodeURIComponent(location.href)+'&title='+encodeURIComponent(document.title)+'&description='+encodeURIComponent(m?m.content:'')+'&image='+encodeURIComponent(i?i.content:'')+'&tags='+encodeURIComponent(k?k.content:'');window.open('{$baseUrl}/submit?'+q,'_blank');})();";
    ?>
    <a class="btn btn-outline-info" href="<?= Helpers::e($bookmarklet) ?>">Intranet Submit</a>
</div>
