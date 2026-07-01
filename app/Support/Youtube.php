<?php

namespace App\Support;

class Youtube
{
    /**
     * Converte um link comum do YouTube (playlist, video ou ja um embed)
     * na URL de embed correta para uso em iframe.
     */
    public static function embedUrl(?string $url): ?string
    {
        $url = trim((string) $url);

        if ($url === '') {
            return null;
        }

        if (str_contains($url, '/embed/')) {
            return $url;
        }

        if (preg_match('/[?&]list=([A-Za-z0-9_-]+)/', $url, $matches)) {
            return 'https://www.youtube.com/embed/videoseries?list='.$matches[1];
        }

        if (preg_match('#youtu\.be/([A-Za-z0-9_-]+)#', $url, $matches)) {
            return 'https://www.youtube.com/embed/'.$matches[1];
        }

        if (preg_match('/[?&]v=([A-Za-z0-9_-]+)/', $url, $matches)) {
            return 'https://www.youtube.com/embed/'.$matches[1];
        }

        return null;
    }
}
