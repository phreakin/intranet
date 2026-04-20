<?php

declare(strict_types=1);

namespace Intranet\Modules\Shared\Services;

final class MetadataExtractorService
{
    public function extract(string $url): array
    {
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            throw new \InvalidArgumentException('Invalid URL provided.');
        }

        $context = stream_context_create([
            'http' => [
                'timeout' => 6,
                'follow_location' => 1,
                'max_redirects' => 3,
                'user_agent' => 'IntranetPromptMetadataBot/1.0',
            ],
        ]);

        $html = @file_get_contents($url, false, $context);
        if (!is_string($html) || $html === '') {
            return [
                'title' => parse_url($url, PHP_URL_HOST),
                'description' => '',
                'thumbnail' => '',
                'site_name' => parse_url($url, PHP_URL_HOST),
                'canonical_url' => $url,
                'author' => '',
                'publish_date' => null,
                'keywords' => [],
                'open_graph' => [],
                'twitter_card' => [],
                'raw_error' => 'Metadata fetch failed or timed out.',
            ];
        }

        libxml_use_internal_errors(true);
        $dom = new \DOMDocument();
        @$dom->loadHTML($html);
        $xpath = new \DOMXPath($dom);

        $meta = static function (string $attr, string $key) use ($xpath): string {
            $node = $xpath->query("//meta[@{$attr}='{$key}']")->item(0);
            return $node?->getAttribute('content') ?? '';
        };

        $titleNode = $xpath->query('//title')->item(0);
        $title = trim($meta('property', 'og:title') ?: ($titleNode?->textContent ?? ''));
        $description = trim($meta('property', 'og:description') ?: $meta('name', 'description'));
        $thumbnail = trim($meta('property', 'og:image') ?: $meta('name', 'twitter:image'));
        $siteName = trim($meta('property', 'og:site_name') ?: (parse_url($url, PHP_URL_HOST) ?: ''));
        $canonicalNode = $xpath->query("//link[@rel='canonical']")->item(0);
        $canonical = $canonicalNode?->getAttribute('href') ?: $url;
        $author = trim($meta('name', 'author') ?: $meta('property', 'article:author'));
        $publishDate = trim($meta('property', 'article:published_time') ?: $meta('name', 'date'));
        $keywords = array_values(array_filter(array_map('trim', explode(',', $meta('name', 'keywords')))));

        return [
            'title' => $title ?: parse_url($url, PHP_URL_HOST),
            'description' => $description,
            'thumbnail' => $thumbnail,
            'site_name' => $siteName,
            'canonical_url' => $canonical,
            'author' => $author,
            'publish_date' => $publishDate !== '' ? $publishDate : null,
            'keywords' => $keywords,
            'open_graph' => [
                'title' => $meta('property', 'og:title'),
                'description' => $meta('property', 'og:description'),
                'image' => $meta('property', 'og:image'),
                'type' => $meta('property', 'og:type'),
            ],
            'twitter_card' => [
                'title' => $meta('name', 'twitter:title'),
                'description' => $meta('name', 'twitter:description'),
                'image' => $meta('name', 'twitter:image'),
                'card' => $meta('name', 'twitter:card'),
            ],
            'raw_error' => null,
        ];
    }
}
