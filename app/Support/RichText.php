<?php

namespace App\Support;

final class RichText
{
    private const ALLOWED_TAGS = '<p><br><strong><b><em><i><u><ul><ol><li><h2><h3><h4><blockquote><a>';

    public static function sanitize(?string $html): string
    {
        if ($html === null || trim($html) === '') {
            return '';
        }

        $html = strip_tags($html, self::ALLOWED_TAGS);
        $html = preg_replace('/\s*on\w+\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)/iu', '', $html) ?? $html;
        $html = preg_replace('/\s*(href|src)\s*=\s*([\'"])\s*javascript:[^\'"]*\2/iu', '', $html) ?? $html;
        $html = preg_replace('/\s*(href|src)\s*=\s*([\'"])\s*data:[^\'"]*\2/iu', '', $html) ?? $html;

        return trim($html);
    }

    public static function forDisplay(?string $html): string
    {
        return self::sanitize($html);
    }

    public static function toPlainText(?string $html): string
    {
        if ($html === null || trim($html) === '') {
            return '';
        }

        $text = strip_tags($html);
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = preg_replace('/\s+/u', ' ', $text) ?? $text;

        return trim($text);
    }
}
