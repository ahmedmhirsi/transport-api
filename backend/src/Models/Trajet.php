<?php
namespace App\Models;

use App\Config\Database;

class Trajet {
    public function findAll(array $filters, int $page, int $perPage): array {
        $db = Database::getInstance();
        $query = 'SELECT t.*, p.company_name AS provider_name, v.model AS vehicle_model,
                  t.departure AS departure_city, t.destination AS destination_city
                  FROM trajets t
                  LEFT JOIN transport_providers p ON t.provider_id = p.id
                  LEFT JOIN vehicles_park v ON t.vehicle_id = v.id
                  WHERE 1=1';
        $params = [];

        if (!empty($filters['departure'])) {
            $query .= ' AND t.departure LIKE ?';
            $params[] = '%' . $filters['departure'] . '%';
        }
        if (!empty($filters['destination'])) {
            $query .= ' AND t.destination LIKE ?';
            $params[] = '%' . $filters['destination'] . '%';
        }
        if (!empty($filters['date'])) {
            $query .= ' AND t.departure_date = ?';
            $params[] = $filters['date'];
        }
        if (isset($filters['available']) && $filters['available']) {
            $query .= ' AND t.available_seats > 0';
        }

        $query .= ' ORDER BY t.departure_date ASC, t.departure_time ASC LIMIT ' . (int) $perPage . ' OFFSET ' . (int) (($page - 1) * $perPage);

        $stmt = $db->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function findById(int $id): ?array {
        $db = Database::getInstance();
        $stmt = $db->prepare('SELECT t.*, p.company_name AS provider_name, v.model AS vehicle_model,
                             t.departure AS departure_city, t.destination AS destination_city
                             FROM trajets t
                             LEFT JOIN transport_providers p ON t.provider_id = p.id
                             LEFT JOIN vehicles_park v ON t.vehicle_id = v.id
                             WHERE t.id = ? LIMIT 1');
        $stmt->execute([$id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function countAll(array $filters): int {
        $db = Database::getInstance();
        $query = 'SELECT COUNT(*) FROM trajets t WHERE 1=1';
        $params = [];

        if (!empty($filters['departure'])) {
            $query .= ' AND t.departure LIKE ?';
            $params[] = '%' . $filters['departure'] . '%';
        }
        if (!empty($filters['destination'])) {
            $query .= ' AND t.destination LIKE ?';
            $params[] = '%' . $filters['destination'] . '%';
        }
        if (!empty($filters['date'])) {
            $query .= ' AND t.departure_date = ?';
            $params[] = $filters['date'];
        }
        if (isset($filters['available']) && $filters['available']) {
            $query .= ' AND t.available_seats > 0';
        }

        $stmt = $db->prepare($query);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn();
    }

    public function updateAvailableSeats(int $id, int $change): bool {
        $db = Database::getInstance();
        $stmt = $db->prepare('UPDATE trajets SET available_seats = available_seats + ? WHERE id = ?');
        return $stmt->execute([$change, $id]);
    }
}
