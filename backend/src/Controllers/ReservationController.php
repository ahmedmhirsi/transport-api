<?php
namespace App\Controllers;

use App\Models\Reservation;
use App\Services\ReservationService;
use App\Utils\Response;
use App\Utils\Validator;

class ReservationController {
    public function store(array $user): void {
        $data = json_decode(file_get_contents('php://input'), true) ?? [];
        
        $errors = Validator::validate($data, [
            'trajet_id' => 'required|integer',
            'seats' => 'required|integer|min:1'
        ]);
        
        if (!empty($errors)) {
            Response::error('VALIDATION_ERROR', 'Invalid input data', 422);
        }
        
        $service = new ReservationService();
        $result = $service->createReservation($user['user_id'], (int)$data['trajet_id'], (int)$data['seats']);
        
        if (!$result['success']) {
            Response::error('RESERVATION_ERROR', $result['error'], $result['code'] ?? 400);
        }
        
        Response::success(['reservation_id' => $result['reservation_id']], 201);
    }

    public function index(array $user): void {
        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $perPage = isset($_GET['per_page']) ? max(1, (int)$_GET['per_page']) : 10;
        
        $model = new Reservation();
        $total = $model->countByUserId($user['user_id']);
        $data = $model->findByUserId($user['user_id'], $page, $perPage);
        
        Response::paginated($data, $total, $page, $perPage);
    }

    public function show(int $id, array $user): void {
        $model = new Reservation();
        $reservation = $model->findById($id);
        
        if (!$reservation) {
            Response::error('NOT_FOUND', 'Reservation not found', 404);
        }
        
        if ($reservation['user_id'] !== $user['user_id']) {
            Response::error('FORBIDDEN', 'Access denied', 403);
        }
        
        Response::success($reservation);
    }

    public function cancel(int $id, array $user): void {
        $service = new ReservationService();
        $result = $service->cancelReservation($id, $user['user_id']);
        
        if (!$result['success']) {
            Response::error('CANCEL_ERROR', $result['error'], $result['code'] ?? 400);
        }
        
        Response::success(['message' => 'Reservation cancelled successfully']);
    }

    public function stats(array $user): void {
        $model = new Reservation();
        $stats = $model->getStats($user['user_id']);

        Response::success([
            'total_reservations' => array_sum(array_values($stats)),
            'confirmed_reservations' => (int) ($stats['confirmed'] ?? 0),
            'cancelled_reservations' => (int) ($stats['cancelled'] ?? 0),
            'pending_reservations' => (int) ($stats['pending'] ?? 0),
        ]);
    }
}
