<?php
namespace App\Controllers;

use App\Config\Env;
use App\Models\Webhook;
use App\Utils\Response;
use App\Utils\Logger;

class WebhookController {
    public function receive(): void {
        $headers = getallheaders();
        $signature = $headers['X-Webhook-Signature'] ?? null;
        $payload = file_get_contents('php://input');

        if (!$signature || !str_starts_with($signature, 'sha256=')) {
            Response::error('UNAUTHORIZED', 'Missing or invalid signature', 401);
            return;
        }

        $expected = 'sha256=' . hash_hmac('sha256', $payload, Env::get('WEBHOOK_SECRET', 'webhook-secret'));
        if (!hash_equals($expected, $signature)) {
            Response::error('UNAUTHORIZED', 'Invalid webhook signature', 401);
            return;
        }

        $decodedPayload = json_decode($payload, true);
        if (!is_array($decodedPayload)) {
            Response::error('INVALID_PAYLOAD', 'Malformed webhook payload', 400);
            return;
        }

        Logger::info('Webhook received: ' . $payload);
        Response::success([
            'message' => 'Webhook received successfully',
            'event' => $decodedPayload['event'] ?? 'unknown',
            'timestamp' => $decodedPayload['timestamp'] ?? null,
        ]);
    }

    public function events(): void {
        $page = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;
        $perPage = isset($_GET['per_page']) ? max(1, (int) $_GET['per_page']) : 10;

        $model = new Webhook();
        $total = $model->countEvents();
        $events = $model->getEvents($page, $perPage);

        Response::paginated($events, $total, $page, $perPage);
    }
}
