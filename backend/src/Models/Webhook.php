<?php
namespace App\Models;

use App\Config\Database;

class Webhook {
    public function findActive(): array {
        $db = Database::getInstance();
        $stmt = $db->prepare('SELECT * FROM webhooks WHERE active = 1');
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function createEvent(int $webhookId, string $eventType, array $payload): int {
        $db = Database::getInstance();
        $stmt = $db->prepare('INSERT INTO webhook_events (webhook_id, event_type, payload, status) VALUES (?, ?, ?, "pending")');
        $stmt->execute([$webhookId, $eventType, json_encode($payload)]);
        return (int) $db->lastInsertId();
    }

    public function updateEventStatus(int $id, string $status, ?string $sentAt): void {
        $db = Database::getInstance();
        if ($sentAt) {
            $stmt = $db->prepare('UPDATE webhook_events SET status = ?, sent_at = ? WHERE id = ?');
            $stmt->execute([$status, $sentAt, $id]);
        } else {
            $stmt = $db->prepare('UPDATE webhook_events SET status = ? WHERE id = ?');
            $stmt->execute([$status, $id]);
        }
    }

    public function getEvents(int $page, int $perPage): array {
        $db = Database::getInstance();
        $offset = ($page - 1) * $perPage;
        $stmt = $db->prepare('SELECT * FROM webhook_events ORDER BY created_at DESC LIMIT ' . (int)$perPage . ' OFFSET ' . (int)$offset);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getEventsByWebhook(int $webhookId): array {
        $db = Database::getInstance();
        $stmt = $db->prepare('SELECT * FROM webhook_events WHERE webhook_id = ? ORDER BY created_at DESC');
        $stmt->execute([$webhookId]);
        return $stmt->fetchAll();
    }

    public function countEvents(): int {
        $db = Database::getInstance();
        $stmt = $db->prepare('SELECT COUNT(*) FROM webhook_events');
        $stmt->execute();
        return (int) $stmt->fetchColumn();
    }
}
