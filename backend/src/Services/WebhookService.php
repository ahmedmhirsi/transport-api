<?php
namespace App\Services;

use App\Models\Webhook;
use App\Utils\Logger;
use Exception;

class WebhookService {
    public function trigger(string $eventType, array $data): void {
        $webhookModel = new Webhook();
        $activeWebhooks = $webhookModel->findActive();
        
        $payload = [
            'event' => $eventType,
            'timestamp' => date('c'),
            'data' => $data
        ];
        
        foreach ($activeWebhooks as $webhook) {
            $eventId = $webhookModel->createEvent($webhook['id'], $eventType, $payload);
            
            $payloadJson = json_encode($payload);
            $signature = hash_hmac('sha256', $payloadJson, $webhook['secret']);
            
            $this->sendWebhook($webhook['endpoint_url'], $payloadJson, $signature, $eventId);
        }
    }

    private function sendWebhook(string $url, string $payload, string $signature, int $eventId): void {
        $context = stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => [
                    'Content-Type: application/json',
                    'X-Webhook-Signature: sha256=' . $signature
                ],
                'content' => $payload,
                'timeout' => 5,
                'ignore_errors' => true
            ]
        ]);
        
        try {
            $result = @file_get_contents($url, false, $context);
            $webhookModel = new Webhook();
            if ($result !== false) {
                $webhookModel->updateEventStatus($eventId, 'sent', date('Y-m-d H:i:s'));
            } else {
                $webhookModel->updateEventStatus($eventId, 'failed', null);
            }
        } catch (Exception $e) {
            $webhookModel = new Webhook();
            $webhookModel->updateEventStatus($eventId, 'failed', null);
            Logger::error('Webhook delivery failed: ' . $e->getMessage());
        }
    }
}
