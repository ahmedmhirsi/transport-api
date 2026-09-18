<?php
namespace App;

use App\Middleware\CorsMiddleware;
use App\Middleware\RateLimitMiddleware;
use App\Middleware\JWTMiddleware;
use App\Middleware\ApiKeyMiddleware;
use App\Controllers\AuthController;
use App\Controllers\TrajetController;
use App\Controllers\ReservationController;
use App\Controllers\WebhookController;
use App\Controllers\ThirdPartyController;
use App\Utils\Response;

class Router {
    public static function handle(): void {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        $uri = preg_replace('#^/(?:.*?/)?backend/public#', '', $uri ?? '');
        $uri = rtrim($uri, '/');
        if ($uri === '') $uri = '/';

        CorsMiddleware::handle();
        
        // Skip rate limit for frontend static pages and swagger
        if (str_starts_with($uri, '/api')) {
            RateLimitMiddleware::handle();
        }
        
        switch (true) {
            case $method === 'POST' && $uri === '/api/login':
                (new AuthController())->login();
                break;
            
            case $method === 'GET' && $uri === '/api/trajets':
                $user = JWTMiddleware::handle();
                (new TrajetController())->index();
                break;
            
            case $method === 'POST' && $uri === '/api/reservations':
                $user = JWTMiddleware::handle();
                (new ReservationController())->store($user);
                break;
            
            case $method === 'GET' && $uri === '/api/reservations':
                $user = JWTMiddleware::handle();
                (new ReservationController())->index($user);
                break;
            
            case $method === 'GET' && $uri === '/api/reservations/stats':
                $user = JWTMiddleware::handle();
                (new ReservationController())->stats($user);
                break;
            
            case $method === 'GET' && preg_match('#^/api/reservations/(\d+)$#', $uri, $m):
                $user = JWTMiddleware::handle();
                (new ReservationController())->show((int)$m[1], $user);
                break;
            
            case $method === 'POST' && preg_match('#^/api/reservations/(\d+)/cancel$#', $uri, $m):
                $user = JWTMiddleware::handle();
                (new ReservationController())->cancel((int)$m[1], $user);
                break;
            
            case $method === 'POST' && $uri === '/api/webhooks/receive':
                (new WebhookController())->receive();
                break;
            
            case $method === 'GET' && $uri === '/api/webhooks/events':
                $user = JWTMiddleware::handle();
                (new WebhookController())->events();
                break;
            
            case $method === 'GET' && $uri === '/api/third-party/reservations':
                $app = ApiKeyMiddleware::handle();
                (new ThirdPartyController())->reservations($app);
                break;
            
            case $method === 'GET' && preg_match('#^/api/third-party/reservations/(\d+)$#', $uri, $m):
                $app = ApiKeyMiddleware::handle();
                (new ThirdPartyController())->reservationDetail((int)$m[1], $app);
                break;
            
            case $method === 'GET' && $uri === '/api/third-party/webhook-events':
                $app = ApiKeyMiddleware::handle();
                (new ThirdPartyController())->webhookEvents($app);
                break;
            
            case $method === 'GET' && $uri === '/api/third-party/stats':
                $app = ApiKeyMiddleware::handle();
                (new ThirdPartyController())->stats($app);
                break;
            
            case $method === 'GET' && $uri === '/swagger':
                include __DIR__ . '/../public/swagger.html';
                break;
                
            case $method === 'GET' && $uri === '/api/openapi.yaml':
                header('Content-Type: text/yaml');
                $yamlPath = __DIR__ . '/../swagger/openapi.yaml';
                if (file_exists($yamlPath)) {
                    readfile($yamlPath);
                } else {
                    Response::error('NOT_FOUND', 'OpenAPI spec not found', 404);
                }
                break;
            
            case $method === 'GET' && ($uri === '/' || $uri === '/app'):
                header('Location: /app/index.html');
                exit;
                break;
            
            default:
                if (str_starts_with($uri, '/api')) {
                    Response::error('NOT_FOUND', 'Endpoint not found', 404);
                } else {
                    http_response_code(404);
                    echo "Not Found";
                }
                break;
        }
    }
}
