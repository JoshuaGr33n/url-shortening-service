<?php

namespace App\Repositories;

use Illuminate\Support\Facades\Cache;


/**
 * Repository class for URL shortening.
 */
class UrlShortenerRepository implements UrlShortenerRepositoryInterface
{
    // Arrays to store URL mappings
    protected $urlKeyMap = [];
    protected $keyUrlMap = [];

    /**
     * Constructor to initialize URL mappings from cache.
     */
    public function __construct()
    {
        $this->urlKeyMap = Cache::get('urlKeyMap', []);
        $this->keyUrlMap = Cache::get('keyUrlMap', []);
    }

    /**
     * Encodes a URL to a short key.
     *
     * @param string $url The original URL to encode.
     * @return string The generated short key.
     */
    public function encode(string $url, string $generateKey): string
    {
        // Check if the URL is already encoded
        if (isset($this->urlKeyMap[$url])) {
            return $this->urlKeyMap[$url];
        }

        // Generate a unique key
        $key = $generateKey;
        while (isset($this->keyUrlMap[$key])) {
            $key = $generateKey;
        }

        // Save the URL and key mappings
        $this->urlKeyMap[$url] = $key;
        $this->keyUrlMap[$key] = $url;

        // Update cache
        Cache::put('urlKeyMap', $this->urlKeyMap);
        Cache::put('keyUrlMap', $this->keyUrlMap);

        return $key;
    }

    /**
     * Decodes a short key back to the original URL.
     *
     * @param string $key The short key to decode.
     * @return string|null The original URL or null if not found.
     */
    public function decode(string $key): ?string
    {
        return $this->keyUrlMap[$key] ?? null;
    }

    
}
