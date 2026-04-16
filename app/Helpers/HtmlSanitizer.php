<?php

namespace App\Helpers;

class HtmlSanitizer
{
    /**
     * Allowed HTML tags for blog content.
     */
    private const ALLOWED_TAGS = '<p><br><h1><h2><h3><h4><h5><h6><ul><ol><li><a><strong><em><b><i><blockquote><code><pre><img><table><thead><tbody><tr><th><td><hr><span><div><figure><figcaption>';

    /**
     * Allowed attributes per tag. Any attribute not listed here is stripped.
     */
    private const ALLOWED_ATTRIBUTES = [
        'a'     => ['href', 'title', 'target', 'rel'],
        'img'   => ['src', 'alt', 'width', 'height', 'loading'],
        'td'    => ['colspan', 'rowspan'],
        'th'    => ['colspan', 'rowspan'],
        'span'  => ['class'],
        'div'   => ['class'],
        'pre'   => ['class'],
        'code'  => ['class'],
        'blockquote' => ['class'],
        'figure'     => ['class'],
    ];

    /**
     * Sanitize HTML: strip disallowed tags, remove dangerous attributes,
     * block javascript: URLs, and remove event handlers.
     */
    public static function clean(string $html): string
    {
        // Step 1: Strip disallowed tags
        $html = strip_tags($html, self::ALLOWED_TAGS);

        // Step 2: Remove all on* event handlers (onclick, onload, onerror, etc.)
        $html = preg_replace('/\s+on\w+\s*=\s*["\'][^"\']*["\']/i', '', $html);
        $html = preg_replace('/\s+on\w+\s*=\s*\S+/i', '', $html);

        // Step 3: Remove javascript:, vbscript:, data: URLs from href/src attributes
        $html = preg_replace_callback(
            '/(href|src)\s*=\s*["\']?\s*(javascript|vbscript|data)\s*:/i',
            fn () => 'href="#"',
            $html
        );

        // Step 4: Remove attributes not in the allowed list
        $html = preg_replace_callback(
            '/<(\w+)(\s[^>]*)>/i',
            function ($matches) {
                $tag = strtolower($matches[1]);
                $attrs = $matches[2];

                if (!isset(self::ALLOWED_ATTRIBUTES[$tag])) {
                    // No attributes allowed for this tag — strip them all
                    return "<{$matches[1]}>";
                }

                $allowed = self::ALLOWED_ATTRIBUTES[$tag];
                $cleanAttrs = '';

                // Extract and keep only allowed attributes
                preg_match_all('/\s+(\w[\w-]*)\s*=\s*(?:"([^"]*)"|\'([^\']*)\'|(\S+))/', $attrs, $attrMatches, PREG_SET_ORDER);

                foreach ($attrMatches as $attr) {
                    $name = strtolower($attr[1]);
                    $value = $attr[2] ?? $attr[3] ?? $attr[4] ?? '';

                    if (in_array($name, $allowed)) {
                        // Extra check: no javascript/vbscript in href/src
                        if (in_array($name, ['href', 'src'])) {
                            $trimmed = trim(strtolower($value));
                            if (preg_match('/^(javascript|vbscript|data):/i', $trimmed)) {
                                continue;
                            }
                        }
                        $cleanAttrs .= " {$name}=\"" . htmlspecialchars($value, ENT_QUOTES, 'UTF-8', false) . '"';
                    }
                }

                return "<{$matches[1]}{$cleanAttrs}>";
            },
            $html
        );

        // Step 5: Force rel="noopener noreferrer" and target="_blank" on external links
        $html = preg_replace_callback(
            '/<a\s([^>]*)>/i',
            function ($matches) {
                $attrs = $matches[1];
                if (!str_contains($attrs, 'rel=')) {
                    $attrs .= ' rel="noopener noreferrer"';
                }
                if (!str_contains($attrs, 'target=')) {
                    $attrs .= ' target="_blank"';
                }
                return "<a {$attrs}>";
            },
            $html
        );

        return $html;
    }
}
