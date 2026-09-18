<?php
namespace App\Utils;

class Logger {
    private static function getLogFile(): string {
        $dir = __DIR__ . '/../../storage/logs';
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        return $dir . '/' . date('Y-m-d') . '.log';
    }

    private static function log(string $level, string $message): void {
        $date = date('Y-m-d H:i:s');
        $formatted = "[$date] [$level] $message" . PHP_EOL;
        file_put_contents(self::getLogFile(), $formatted, FILE_APPEND);
    }

    public static function info(string $message): void {
        self::log('INFO', $message);
    }

    public static function warning(string $message): void {
        self::log('WARNING', $message);
    }

    public static function error(string $message): void {
        self::log('ERROR', $message);
    }
}
