<?php

declare(strict_types=1);

namespace App\Infrastructure\DocFetcher;

class HtmlSanitizer
{
    public function sanitize(string $html): string
    {
        $cleaned = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $html);
        $cleaned = is_string($cleaned) ? $cleaned : $html;

        $cleaned = preg_replace('/\son\w+\s*=\s*"[^"]*"/i', '', $cleaned);
        $cleaned = is_string($cleaned) ? $cleaned : $html;

        $cleaned = preg_replace("/\son\w+\s*=\s*'[^']*'/i", '', $cleaned);

        return is_string($cleaned) ? $cleaned : $html;
    }
}
