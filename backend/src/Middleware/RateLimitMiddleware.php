<?php
namespace App\Middleware;

use App\Config\Database;
use App\Config\Env;
use App\Utils\Response;

class RateLimitMiddleware {
    public static function handle(): void {
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $headers = getallheaders();
        $identifier = $headers['X-API-Key'] ?? $ip;

        $max = (int) Env::get('RATE_LIMIT_MAX', '100');
        $window = (int) Env::get('RATE_LIMIT_WINDOW', '60');

        $db = Database::getInstance();

        $stmt = $db->prepare('DELETE FROM rate_limits WHERE expires_at < NOW()');
        $stmt->execute();

        $stmt = $db->prepare('SELECT id, hits, expires_at FROM rate_limits WHERE identifier = ?');
        $stmt->execute([$identifier]);
        $record = $stmt->fetch();

        if (!$record) {
            $stmt = $db->prepare('INSERT INTO rate_limits (identifier, hits, expires_at) VALUES (?, 1, DATE_ADD(NOW(), INTERVAL ? SECOND))');
            $stmt->execute([$identifier, $window]);
            $remaining = $max - 1;
            $reset = time() + $window;
        } else {
            if ((int) $record['hits'] >= $max) {
                $retryAfter = max(1, (int) strtotime($record['expires_at']) - time());
                header('X-RateLimit-Limit: ' . $max);
                header('X-RateLimit-Remaining: 0');
                header('X-RateLimit-Reset: ' . strtotime($record['expires_at']));
                Response::json([
                    'success' => false,
                    'error' => 'Too many requests',
                    'retry_after' => $retryAfter,
                ], 429);
            }

            $stmt = $db->prepare('UPDATE rate_limits SET hits = hits + 1 WHERE id = ?');
            $stmt->execute([$record['id']]);
            $remaining = $max - (int) $record['hits'] - 1;
            $reset = strtotime($record['expires_at']);
        }

        header('X-RateLimit-Limit: ' . $max);
        header('X-RateLimit-Remaining: ' . max(0, $remaining));
        header('X-RateLimit-Reset: ' . $reset);
    }
}
