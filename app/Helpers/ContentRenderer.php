<?php

namespace App\Helpers;

class ContentRenderer
{
    /**
     * Render content with embedded media from URLs
     */
    public static function render(string $content): string
    {
        // First, escape HTML to prevent XSS
        $content = e($content);

        // Pattern to match URLs
        $urlPattern = '/(https?:\/\/[^\s<>"\']+)/i';

        // Replace URLs with appropriate embeds
        $content = preg_replace_callback($urlPattern, function ($matches) {
            $url = $matches[1];

            // Check if it's a direct image/gif link
            if (self::isImageUrl($url)) {
                return self::renderImage($url);
            }

            // Check if it's a Tenor GIF
            if (self::isTenorUrl($url)) {
                return self::renderTenorGif($url);
            }

            // Check if it's a Giphy GIF
            if (self::isGiphyUrl($url)) {
                return self::renderGiphyGif($url);
            }

            // Check if it's a YouTube video
            if (self::isYouTubeUrl($url)) {
                return self::renderYouTube($url);
            }

            // Regular link
            return '<a href="' . $url . '" target="_blank" rel="noopener noreferrer" class="text-success">' . $url . '</a>';
        }, $content);

        // Convert newlines to <br>
        $content = nl2br($content);

        return $content;
    }

    /**
     * Check if URL is a direct image link
     */
    protected static function isImageUrl(string $url): bool
    {
        $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'svg'];
        $parsedUrl = parse_url(strtolower($url));
        $path = $parsedUrl['path'] ?? '';
        $extension = pathinfo($path, PATHINFO_EXTENSION);

        // Also check for image URLs with query strings (like some CDNs)
        if (in_array($extension, $imageExtensions)) {
            return true;
        }

        // Check for common image CDN patterns
        if (preg_match('/\.(jpg|jpeg|png|gif|webp)/i', $url)) {
            return true;
        }

        return false;
    }

    /**
     * Check if URL is a Tenor GIF
     */
    protected static function isTenorUrl(string $url): bool
    {
        return (bool) preg_match('/tenor\.com\/(view|gif)/i', $url) ||
               (bool) preg_match('/media\.tenor\.com/i', $url);
    }

    /**
     * Check if URL is a Giphy GIF
     */
    protected static function isGiphyUrl(string $url): bool
    {
        return (bool) preg_match('/giphy\.com/i', $url) ||
               (bool) preg_match('/media\d*\.giphy\.com/i', $url);
    }

    /**
     * Check if URL is a YouTube video
     */
    protected static function isYouTubeUrl(string $url): bool
    {
        return (bool) preg_match('/(youtube\.com\/watch|youtu\.be\/)/i', $url);
    }

    /**
     * Render an image
     */
    protected static function renderImage(string $url): string
    {
        return '<div class="embedded-media my-2">
                    <a href="' . $url . '" target="_blank">
                        <img src="' . $url . '" alt="Embedded image" class="img-fluid rounded" style="max-height: 400px; max-width: 100%;">
                    </a>
                </div>';
    }

    /**
     * Render Tenor GIF - try to extract direct GIF URL or show as image
     */
    protected static function renderTenorGif(string $url): string
    {
        // If it's already a direct media URL from Tenor
        if (preg_match('/media\.tenor\.com/i', $url)) {
            return '<div class="embedded-media my-2">
                        <img src="' . $url . '" alt="Tenor GIF" class="img-fluid rounded" style="max-height: 400px; max-width: 100%;">
                    </div>';
        }

        // For Tenor page links, show as a clickable link with indicator
        return '<div class="embedded-media my-2">
                    <a href="' . $url . '" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-info">
                        🎬 View GIF on Tenor
                    </a>
                </div>';
    }

    /**
     * Render Giphy GIF
     */
    protected static function renderGiphyGif(string $url): string
    {
        // If it's a direct media URL from Giphy
        if (preg_match('/media\d*\.giphy\.com/i', $url)) {
            return '<div class="embedded-media my-2">
                        <img src="' . $url . '" alt="Giphy GIF" class="img-fluid rounded" style="max-height: 400px; max-width: 100%;">
                    </div>';
        }

        // Try to convert Giphy page URL to embed
        if (preg_match('/giphy\.com\/gifs\/(?:.*-)?([a-zA-Z0-9]+)$/i', $url, $matches)) {
            $gifId = $matches[1];
            return '<div class="embedded-media my-2">
                        <iframe src="https://giphy.com/embed/' . $gifId . '" width="480" height="270" frameborder="0" class="giphy-embed rounded" allowfullscreen></iframe>
                    </div>';
        }

        // Fallback to link
        return '<div class="embedded-media my-2">
                    <a href="' . $url . '" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-info">
                        🎬 View GIF on Giphy
                    </a>
                </div>';
    }

    /**
     * Render YouTube video
     */
    protected static function renderYouTube(string $url): string
    {
        $videoId = null;

        // Match youtube.com/watch?v=VIDEO_ID
        if (preg_match('/youtube\.com\/watch\?v=([a-zA-Z0-9_-]+)/i', $url, $matches)) {
            $videoId = $matches[1];
        }
        // Match youtu.be/VIDEO_ID
        elseif (preg_match('/youtu\.be\/([a-zA-Z0-9_-]+)/i', $url, $matches)) {
            $videoId = $matches[1];
        }

        if ($videoId) {
            return '<div class="embedded-media my-2">
                        <div class="ratio ratio-16x9" style="max-width: 560px;">
                            <iframe src="https://www.youtube.com/embed/' . $videoId . '" title="YouTube video" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen class="rounded"></iframe>
                        </div>
                    </div>';
        }

        // Fallback to link
        return '<a href="' . $url . '" target="_blank" rel="noopener noreferrer" class="text-success">' . $url . '</a>';
    }
}

