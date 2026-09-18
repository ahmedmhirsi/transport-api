<?php
namespace App\Models;

use App\Config\Database;

class Reservation {
    public function create(int $userId, int $trajetId, int $seats): int {
        $db = Database::getInstance();
        $stmt = $db->prepare('INSERT INTO reservations (user_id, trajet_id, seats, status) VALUES (?, ?, ?, "confirmed")');
        $stmt->execute([$userId, $trajetId, $seats]);
        return (int) $db->lastInsertId();
    }

    public function findByUserId(int $userId, int $page, int $perPage): array {
        $db = Database::getInstance();
        $offset = ($page - 1) * $perPage;
        $stmt = $db->prepare('SELECT r.*, t.departure, t.destination, t.departure_time 
                              FROM reservations r 
                              JOIN trajets t ON r.trajet_id = t.id 
                              WHERE r.user_id = ? 
                              ORDER BY r.created_at DESC 
                              LIMIT ' . (int)$perPage . ' OFFSET ' . (int)$offset);
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public function findById(int $id): ?array {
        $db = Database::getInstance();
        $stmt = $db->prepare('SELECT r.*, t.departure, t.destination, t.departure_time 
                              FROM reservations r 
                              JOIN trajets t ON r.trajet_id = t.id 
                              WHERE r.id = ? LIMIT 1');
        $stmt->execute([$id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function countByUserId(int $userId): int {
        $db = Database::getInstance();
        $stmt = $db->prepare('SELECT COUNT(*) FROM reservations WHERE user_id = ?');
        $stmt->execute([$userId]);
        return (int) $stmt->fetchColumn();
    }

    public function updateStatus(int $id, string $status): bool {
        $db = Database::getInstance();
        $stmt = $db->prepare('UPDATE reservations SET status = ? WHERE id = ?');
        return $stmt->execute([$status, $id]);
    }

    public function getStats(int $userId): array {
        $db = Database::getInstance();
        $stmt = $db->prepare('SELECT status, COUNT(*) as count FROM reservations WHERE user_id = ? GROUP BY status');
        $stmt->execute([$userId]);
        return $stmt->fetchAll(\PDO::FETCH_KEY_PAIR);
    }
}
