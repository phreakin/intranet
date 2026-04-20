<?php

declare(strict_types=1);

namespace Intranet\Modules\Shared\Services;

use Intranet\Core\Helpers;

final class MarkdownService
{
    public function render(string $markdown): string
    {
        $escaped = Helpers::e($markdown);
        $escaped = preg_replace('/\r\n?/', "\n", $escaped) ?? $escaped;
        $escaped = preg_replace('/^###\s+(.+)$/m', '<h3>$1</h3>', $escaped) ?? $escaped;
        $escaped = preg_replace('/^##\s+(.+)$/m', '<h2>$1</h2>', $escaped) ?? $escaped;
        $escaped = preg_replace('/^#\s+(.+)$/m', '<h1>$1</h1>', $escaped) ?? $escaped;
        $escaped = preg_replace('/\*\*(.+?)\*\*/s', '<strong>$1</strong>', $escaped) ?? $escaped;
        $escaped = preg_replace('/\*(.+?)\*/s', '<em>$1</em>', $escaped) ?? $escaped;
        $escaped = preg_replace('/`(.+?)`/s', '<code>$1</code>', $escaped) ?? $escaped;
        $escaped = preg_replace('/\[(.+?)\]\((https?:\/\/[^\s)]+)\)/', '<a href="$2" target="_blank" rel="noreferrer">$1</a>', $escaped) ?? $escaped;
        $escaped = preg_replace('/^(?:-|\*)\s+(.+)$/m', '<li>$1</li>', $escaped) ?? $escaped;
        $escaped = preg_replace('/(<li>.*<\/li>)/sU', '<ul>$1</ul>', $escaped) ?? $escaped;

        $paragraphs = array_filter(array_map('trim', explode("\n\n", $escaped)));
        $html = [];
        foreach ($paragraphs as $block) {
            if (preg_match('/^<(h1|h2|h3|ul|li)/', $block) === 1) {
                $html[] = $block;
                continue;
            }
            $html[] = '<p>' . nl2br($block) . '</p>';
        }

        return implode("\n", $html);
    }
}
