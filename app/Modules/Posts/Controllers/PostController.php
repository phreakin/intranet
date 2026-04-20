<?php

declare(strict_types=1);

namespace Intranet\Modules\Posts\Controllers;

use Intranet\Core\Auth;
use Intranet\Core\Csrf;
use Intranet\Core\Helpers;
use Intranet\Core\View;
use Intranet\Modules\Shared\Repositories\PostRepository;
use Intranet\Modules\Shared\Services\AiModerationService;
use Intranet\Modules\Shared\Services\MetadataExtractorService;
use Intranet\Modules\Shared\Services\PostStatusTaggingService;

final class PostController
{
    public function show(int $id): void
    {
        $repo = new PostRepository();
        $post = $repo->find($id);
        if (!$post) {
            http_response_code(404);
            View::render('errors/404', ['title' => 'Post not found']);
            return;
        }

        View::render('posts/show', [
            'title' => $post['title'],
            'post' => $post,
            'comments' => $repo->comments($id),
            'csrf' => Csrf::token(),
        ]);
    }

    public function createForm(): void
    {
        Auth::requireAuth();
        $url = trim((string) ($_GET['url'] ?? ''));
        $prefill = [
            'url' => $url,
            'title' => trim((string) ($_GET['title'] ?? '')),
            'description' => trim((string) ($_GET['description'] ?? '')),
            'thumbnail_url' => trim((string) ($_GET['image'] ?? '')),
            'tags' => trim((string) ($_GET['tags'] ?? '')),
            'site_name' => '',
            'canonical_url' => $url,
            'author_name' => '',
            'published_at' => '',
        ];
        $metaError = null;
        if ($url !== '' && filter_var($url, FILTER_VALIDATE_URL)) {
            try {
                $meta = (new MetadataExtractorService())->extract($url);
            } catch (\Throwable $e) {
                $meta = [];
                $metaError = 'Metadata fetch blocked for this URL.';
            }
            $prefill['title'] = $prefill['title'] !== '' ? $prefill['title'] : (string) ($meta['title'] ?? '');
            $prefill['description'] = $prefill['description'] !== '' ? $prefill['description'] : (string) ($meta['description'] ?? '');
            $prefill['thumbnail_url'] = $prefill['thumbnail_url'] !== '' ? $prefill['thumbnail_url'] : (string) ($meta['thumbnail'] ?? '');
            $prefill['site_name'] = (string) ($meta['site_name'] ?? '');
            $prefill['canonical_url'] = (string) ($meta['canonical_url'] ?? $url);
            $prefill['author_name'] = (string) ($meta['author'] ?? '');
            $prefill['published_at'] = (string) ($meta['publish_date'] ?? '');
            if ($prefill['tags'] === '' && !empty($meta['keywords'])) {
                $prefill['tags'] = implode(', ', (array) $meta['keywords']);
            }
            if (!empty($meta['raw_error'])) {
                $metaError = (string) $meta['raw_error'];
            }
        } elseif ($url !== '') {
            $metaError = 'Please enter a valid URL to fetch metadata.';
        }

        View::render('posts/submit', [
            'title' => 'Submit Link',
            'categories' => (new PostRepository())->categories(),
            'csrf' => Csrf::token(),
            'prefill' => $prefill,
            'metaError' => $metaError,
        ]);
    }

    public function create(): void
    {
        Auth::requireAuth();
        if (!Csrf::check($_POST['_csrf'] ?? null)) {
            http_response_code(422);
            exit('Invalid CSRF token.');
        }

        $url = trim((string) ($_POST['url'] ?? ''));
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            View::render('posts/submit', [
                'title' => 'Submit Link',
                'error' => 'Please enter a valid URL.',
                'categories' => (new PostRepository())->categories(),
                'csrf' => Csrf::token(),
            ]);
            return;
        }

        try {
            $meta = (new MetadataExtractorService())->extract($url);
        } catch (\Throwable $e) {
            View::render('posts/submit', [
                'title' => 'Submit Link',
                'error' => 'Metadata fetch blocked for this URL.',
                'categories' => (new PostRepository())->categories(),
                'csrf' => Csrf::token(),
                'prefill' => ['url' => $url],
            ]);
            return;
        }
        $title = trim((string) ($_POST['title'] ?? ''));
        $description = trim((string) ($_POST['description'] ?? ''));
        $thumbnailUrl = trim((string) ($_POST['thumbnail_url'] ?? ''));
        $siteName = trim((string) ($_POST['site_name'] ?? ''));
        $canonicalUrl = trim((string) ($_POST['canonical_url'] ?? ''));
        $authorName = trim((string) ($_POST['author_name'] ?? ''));
        $publishedAt = trim((string) ($_POST['published_at'] ?? ''));

        $repo = new PostRepository();
        $categoryId = (int) ($_POST['category_id'] ?? 0);
        $newCategory = trim((string) ($_POST['new_category'] ?? ''));
        if ($newCategory !== '') {
            $categoryId = $repo->createCategoryIfMissing($newCategory);
        }

        $manualTags = array_values(array_filter(array_map('trim', explode(',', (string) ($_POST['tags'] ?? '')))));
        $tags = $manualTags !== [] ? $manualTags : (array) ($meta['keywords'] ?? []);

        $canonical = $canonicalUrl !== '' && filter_var($canonicalUrl, FILTER_VALIDATE_URL) ? $canonicalUrl : ((string) ($meta['canonical_url'] ?? $url));
        $thumb = $thumbnailUrl !== '' && filter_var($thumbnailUrl, FILTER_VALIDATE_URL) ? $thumbnailUrl : ((string) ($meta['thumbnail'] ?? ''));
        $publishDate = $this->normalizeDateTime($publishedAt !== '' ? $publishedAt : (string) ($meta['publish_date'] ?? ''));
        $mergedMeta = $meta;
        $mergedMeta['title'] = $title !== '' ? $title : (string) ($meta['title'] ?? $url);
        $mergedMeta['description'] = $description !== '' ? $description : (string) ($meta['description'] ?? '');
        $mergedMeta['thumbnail'] = $thumb;
        $mergedMeta['site_name'] = $siteName !== '' ? $siteName : (string) ($meta['site_name'] ?? '');
        $mergedMeta['canonical_url'] = $canonical;
        $mergedMeta['author'] = $authorName !== '' ? $authorName : (string) ($meta['author'] ?? '');
        $mergedMeta['publish_date'] = $publishDate;
        $mergedMeta['keywords'] = $tags;

        $postId = $repo->create([
            'user_id' => (int) Auth::user()['id'],
            'category_id' => $categoryId > 0 ? $categoryId : null,
            'url' => $url,
            'canonical_url' => $canonical,
            'title' => $mergedMeta['title'],
            'description' => $mergedMeta['description'],
            'thumbnail_url' => $thumb,
            'site_name' => $mergedMeta['site_name'],
            'author_name' => $mergedMeta['author'],
            'published_at' => $publishDate,
            'metadata_json' => json_encode($mergedMeta, JSON_THROW_ON_ERROR),
        ]);

        $repo->syncTags($postId, $tags);

        $post = $repo->find($postId);
        if ($post) {
            $statusTags = (new PostStatusTaggingService())->compute($post);
            $repo->applyStatusTags($postId, $statusTags);
            (new AiModerationService())->analyze('post', $postId, trim(($post['title'] ?? '') . ' ' . ($post['description'] ?? '')));
        }

        Helpers::redirect('/post/' . $postId);
    }

    public function comment(int $id): void
    {
        Auth::requireAuth();
        if (!Csrf::check($_POST['_csrf'] ?? null)) {
            http_response_code(422);
            exit('Invalid CSRF token.');
        }

        $body = trim((string) ($_POST['body'] ?? ''));
        if ($body === '' || mb_strlen($body) > 2000) {
            Helpers::redirect('/post/' . $id);
        }

        $repo = new PostRepository();
        $commentId = $repo->addComment($id, (int) Auth::user()['id'], $body);
        (new AiModerationService())->analyze('comment', $commentId, $body);

        Helpers::redirect('/post/' . $id);
    }

    public function favorite(int $id): void
    {
        Auth::requireAuth();
        if (!Csrf::check($_POST['_csrf'] ?? null)) {
            http_response_code(422);
            exit('Invalid CSRF token.');
        }
        (new PostRepository())->recordInteraction('post_favorites', $id, (int) Auth::user()['id']);
        Helpers::redirect('/post/' . $id);
    }

    public function bookmark(int $id): void
    {
        Auth::requireAuth();
        if (!Csrf::check($_POST['_csrf'] ?? null)) {
            http_response_code(422);
            exit('Invalid CSRF token.');
        }
        (new PostRepository())->recordInteraction('post_bookmarks', $id, (int) Auth::user()['id']);
        Helpers::redirect('/post/' . $id);
    }

    public function like(int $id): void
    {
        Auth::requireAuth();
        if (!Csrf::check($_POST['_csrf'] ?? null)) {
            http_response_code(422);
            exit('Invalid CSRF token.');
        }
        (new PostRepository())->vote($id, (int) Auth::user()['id'], 1);
        Helpers::redirect('/post/' . $id);
    }

    public function dislike(int $id): void
    {
        Auth::requireAuth();
        if (!Csrf::check($_POST['_csrf'] ?? null)) {
            http_response_code(422);
            exit('Invalid CSRF token.');
        }
        (new PostRepository())->vote($id, (int) Auth::user()['id'], -1);
        Helpers::redirect('/post/' . $id);
    }

    public function report(int $id): void
    {
        Auth::requireAuth();
        if (!Csrf::check($_POST['_csrf'] ?? null)) {
            http_response_code(422);
            exit('Invalid CSRF token.');
        }
        $reason = trim((string) ($_POST['reason'] ?? 'Needs review'));
        (new PostRepository())->reportPost($id, (int) Auth::user()['id'], $reason !== '' ? $reason : 'Needs review');
        Helpers::redirect('/post/' . $id);
    }

    public function reportComment(int $postId, int $commentId): void
    {
        Auth::requireAuth();
        if (!Csrf::check($_POST['_csrf'] ?? null)) {
            http_response_code(422);
            exit('Invalid CSRF token.');
        }
        $reason = trim((string) ($_POST['reason'] ?? 'Needs review'));
        (new PostRepository())->reportComment($commentId, (int) Auth::user()['id'], $reason !== '' ? $reason : 'Needs review');
        Helpers::redirect('/post/' . $postId);
    }

    public function category(string $slug): void
    {
        View::render('posts/category', [
            'title' => 'Category · ' . $slug,
            'posts' => (new PostRepository())->byCategory($slug),
            'slug' => $slug,
        ]);
    }

    public function tag(string $slug): void
    {
        View::render('posts/tag', [
            'title' => 'Tag · ' . $slug,
            'posts' => (new PostRepository())->byTag($slug),
            'slug' => $slug,
        ]);
    }

    public function editForm(int $id): void
    {
        Auth::requireAuth();
        $repo = new PostRepository();
        $post = $repo->find($id);
        if (!$post || (int) $post['user_id'] !== (int) Auth::user()['id']) {
            http_response_code(403);
            exit('Forbidden');
        }
        View::render('posts/edit', [
            'title' => 'Edit Post',
            'post' => $post,
            'categories' => $repo->categories(),
            'csrf' => Csrf::token(),
        ]);
    }

    public function edit(int $id): void
    {
        Auth::requireAuth();
        if (!Csrf::check($_POST['_csrf'] ?? null)) {
            http_response_code(422);
            exit('Invalid CSRF token.');
        }
        $title = trim((string) ($_POST['title'] ?? ''));
        $description = trim((string) ($_POST['description'] ?? ''));
        $categoryId = (int) ($_POST['category_id'] ?? 0);
        $tags = array_map('trim', explode(',', (string) ($_POST['tags'] ?? '')));
        (new PostRepository())->updateEditable($id, (int) Auth::user()['id'], $title, $description, $categoryId, $tags);
        Helpers::redirect('/post/' . $id);
    }

    private function normalizeDateTime(string $value): ?string
    {
        if ($value === '') {
            return null;
        }
        $ts = strtotime($value);
        if ($ts === false) {
            return null;
        }
        return date('Y-m-d H:i:s', $ts);
    }
}
