<?php
namespace App\Middleware;

use App\Services\JWTService;
use App\Utils\Response;

class JWTMiddleware {
    public static function handle(): array {
        $headers = getallheaders();
        $authHeader = $headers['Authorization'] ?? null;
        
        if (!$authHeader) {
            Response::error('UNAUTHORIZED', 'Missing Authorization header', 401);
            exit;
        }

        if (!preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            Response::error('UNAUTHORIZED', 'Invalid Bearer format', 401);
            exit;
        }

        $token = $matches[1];
        $payload = JWTService::decode($token);

        if (!$payload) {
            Response::error('UNAUTHORIZED', 'Invalid or expired token', 401);
            exit;
        }

        return [
            'user_id' => $payload['user_id'] ?? null,
            'email' => $payload['email'] ?? null,
            'role' => $payload['role'] ?? null
        ];
    }
}
