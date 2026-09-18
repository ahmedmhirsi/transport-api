<?php
namespace App\Services;

use App\Config\Database;
use App\Utils\Logger;
use Exception;

class ReservationService {
    public function createReservation(int $userId, int $trajetId, int $seats): array {
        $db = Database::getInstance();
        
        try {
            $db->beginTransaction();
            
            $stmt = $db->prepare('SELECT id, available_seats, status FROM trajets WHERE id = ? FOR UPDATE');
            $stmt->execute([$trajetId]);
            $trajet = $stmt->fetch();
            
            if (!$trajet) {
                $db->rollBack();
                return ['success' => false, 'error' => 'Trajet not found', 'code' => 404];
            }
            
            if ($trajet['status'] !== 'active') {
                $db->rollBack();
                return ['success' => false, 'error' => 'Trajet is not active', 'code' => 400];
            }
            
            if ($trajet['available_seats'] < $seats) {
                $db->rollBack();
                return ['success' => false, 'error' => 'Not enough seats available', 'code' => 409];
            }
            
            $stmt = $db->prepare('INSERT INTO reservations (user_id, trajet_id, seats, status) VALUES (?, ?, ?, "confirmed")');
            $stmt->execute([$userId, $trajetId, $seats]);
            $reservationId = $db->lastInsertId();
            
            $stmt = $db->prepare('UPDATE trajets SET available_seats = available_seats - ? WHERE id = ?');
            $stmt->execute([$seats, $trajetId]);
            
            $db->commit();
            
            $webhookService = new WebhookService();
            $webhookService->trigger('reservation.created', [
                'reservation_id' => (int)$reservationId,
                'user_id' => $userId,
                'trajet_id' => $trajetId,
                'seats' => $seats,
                'status' => 'confirmed'
            ]);
            
            return ['success' => true, 'reservation_id' => (int)$reservationId];
        } catch (Exception $e) {
            $db->rollBack();
            Logger::error('Reservation creation failed: ' . $e->getMessage());
            return ['success' => false, 'error' => 'Internal error', 'code' => 500];
        }
    }

    public function cancelReservation(int $reservationId, int $userId): array {
        $db = Database::getInstance();
        
        try {
            $db->beginTransaction();
            
            $stmt = $db->prepare('SELECT id, user_id, trajet_id, seats, status FROM reservations WHERE id = ? FOR UPDATE');
            $stmt->execute([$reservationId]);
            $reservation = $stmt->fetch();
            
            if (!$reservation) {
                $db->rollBack();
                return ['success' => false, 'error' => 'Reservation not found', 'code' => 404];
            }
            
            if ($reservation['user_id'] !== $userId) {
                $db->rollBack();
                return ['success' => false, 'error' => 'Forbidden', 'code' => 403];
            }
            
            if ($reservation['status'] === 'cancelled') {
                $db->rollBack();
                return ['success' => false, 'error' => 'Reservation already cancelled', 'code' => 400];
            }
            
            $stmt = $db->prepare('UPDATE reservations SET status = "cancelled" WHERE id = ?');
            $stmt->execute([$reservationId]);
            
            $stmt = $db->prepare('UPDATE trajets SET available_seats = available_seats + ? WHERE id = ?');
            $stmt->execute([$reservation['seats'], $reservation['trajet_id']]);
            
            $db->commit();
            
            $webhookService = new WebhookService();
            $webhookService->trigger('reservation.cancelled', [
                'reservation_id' => $reservationId,
                'user_id' => $userId,
                'trajet_id' => $reservation['trajet_id'],
                'seats' => $reservation['seats'],
                'status' => 'cancelled'
            ]);
            
            return ['success' => true];
        } catch (Exception $e) {
            $db->rollBack();
            Logger::error('Reservation cancellation failed: ' . $e->getMessage());
            return ['success' => false, 'error' => 'Internal error', 'code' => 500];
        }
    }
}
