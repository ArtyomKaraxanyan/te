<?php

namespace App\Service;

class RateLimiter
{
    private const MAX_REQUESTS = 100;
    private const TIME_WINDOW = 60;

    public static function check(string $identifier): bool
    {
        $cacheFile = sys_get_temp_dir() . '/rate_limit_' . md5($identifier) . '.txt';

        $now = time();
        $requests = [];

        if (file_exists($cacheFile)) {
            $data = file_get_contents($cacheFile);
            $requests = $data ? json_decode($data, true) : [];
        }

        $requests = array_filter($requests, fn($timestamp) => $timestamp > $now - self::TIME_WINDOW);
        if (count($requests) >= self::MAX_REQUESTS) {
            return false;
        }

        $requests[] = $now;
        file_put_contents($cacheFile, json_encode($requests));

        return true;
    }

    public static function getClientId(): string
    {
        return $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    }
}
