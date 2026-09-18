<?php
namespace App\Models;

use App\Config\Database;

class ApiKey {
    public function findByHash(string $hash): ?array {
        $db = Database::getInstance();
        $stmt = $db->prepare('SELECT * FROM api_keys WHERE api_key_hash = ? AND active = 1 LIMIT 1');
        $stmt->execute([$hash]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function updateLastUsed(int $id): void {
        $db = Database::getInstance();
        $stmt = $db->prepare('UPDATE api_keys SET last_used_at = NOW() WHERE id = ?');
        $stmt->execute([$id]);
    }
}
