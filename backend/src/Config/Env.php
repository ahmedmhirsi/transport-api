<?php
namespace App\Config;

class Env {
    private static array $vars = [];
    private static bool $loaded = false;
    
    public static function load(string $path): void {
        if (self::$loaded) return;
        if (!file_exists($path)) return;
        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (str_starts_with(trim($line), '#')) continue;
            if (!str_contains($line, '=')) continue;
            [$key, $value] = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);
            self::$vars[$key] = $value;
            $_ENV[$key] = $value;
        }
        self::$loaded = true;
    }
    
    public static function get(string $key, string $default = ''): string {
        return self::$vars[$key] ?? $_ENV[$key] ?? getenv($key) ?: $default;
    }
}
