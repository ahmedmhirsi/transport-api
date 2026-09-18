<?php
namespace App\Controllers;

use App\Config\Database;
use App\Utils\Response;
use App\Models\Webhook;

class ThirdPartyController {
    public function reservations(array $appInfo): void {
        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $perPage = isset($_GET['per_page']) ? max(1, (int)$_GET['per_page']) : 10;
        
        $db = Database::getInstance();
        $offset = ($page - 1) * $perPage;
        
        $stmt = $db->prepare('SELECT count(*) FROM reservations');
        $stmt->execute();
        $total = (int) $stmt->fetchColumn();
        
        $stmt = $db->prepare('SELECT r.*, u.email as user_email, t.departure, t.destination 
                              FROM reservations r 
                              JOIN users u ON r.user_id = u.id 
                              JOIN trajets t ON r.trajet_id = t.id 
                              ORDER BY r.created_at DESC 
                              LIMIT ' . (int)$perPage . ' OFFSET ' . (int)$offset);
        $stmt->execute();
        $data = $stmt->fetchAll();
        
        Response::paginated($data, $total, $page, $perPage);
    }

    public function reservationDetail(int $id, array $appInfo): void {
        $db = Database::getInstance();
        $stmt = $db->prepare('SELECT r.*, u.email as user_email, t.departure, t.destination, t.departure_time 
                              FROM reservations r 
                              JOIN users u ON r.user_id = u.id 
                              JOIN trajets t ON r.trajet_id = t.id 
                              WHERE r.id = ? LIMIT 1');
        $stmt->execute([$id]);
        $reservation = $stmt->fetch();
        
        if (!$reservation) {
            Response::error('NOT_FOUND', 'Reservation not found', 404);
        }
        
        Response::success($reservation);
    }

    public function webhookEvents(array $appInfo): void {
        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $perPage = isset($_GET['per_page']) ? max(1, (int)$_GET['per_page']) : 10;
        
        $model = new Webhook();
        $total = $model->countEvents();
        $data = $model->getEvents($page, $perPage);
        
        Response::paginated($data, $total, $page, $perPage);
    }

    public function stats(array $appInfo): void {
        $db = Database::getInstance();

        $stmt = $db->query('SELECT COUNT(*) FROM reservations');
        $totalReservations = (int) $stmt->fetchColumn();

        $stmt = $db->query("SELECT COUNT(*) FROM reservations WHERE status = 'confirmed'");
        $confirmedReservations = (int) $stmt->fetchColumn();

        $stmt = $db->query("SELECT COUNT(*) FROM reservations WHERE status = 'cancelled'");
        $cancelledReservations = (int) $stmt->fetchColumn();

        $stmt = $db->query('SELECT COUNT(*) FROM trajets WHERE available_seats > 0');
        $activeTrajets = (int) $stmt->fetchColumn();

        $stmt = $db->query('SELECT COUNT(*) FROM users');
        $totalUsers = (int) $stmt->fetchColumn();

        Response::success([
            'total_reservations' => $totalReservations,
            'confirmed_reservations' => $confirmedReservations,
            'cancelled_reservations' => $cancelledReservations,
            'active_trajets' => $activeTrajets,
            'total_users' => $totalUsers
        ]);
    }
}
