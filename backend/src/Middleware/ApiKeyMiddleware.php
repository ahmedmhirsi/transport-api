<?php
namespace App\Middleware;

use App\Utils\Response;
use App\Models\ApiKey;

class ApiKeyMiddleware {
    public static function handle(): array {
        $headers = getallheaders();
        $apiKey = $headers['X-API-Key'] ?? null;

        if (!$apiKey) {
            Response::error('UNAUTHORIZED', 'Missing X-API-Key header', 401);
            exit;
        }

        $hash = hash('sha256', $apiKey);
        $model = new ApiKey();
        $keyInfo = $model->findByHash($hash);

        if (!$keyInfo || empty($keyInfo['active'])) {
            Response::error('UNAUTHORIZED', 'Invalid or inactive API key', 401);
            exit;
        }

        $model->updateLastUsed($keyInfo['id']);

        return $keyInfo;
    }
}
