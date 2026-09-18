<?php
namespace App\Controllers;

use App\Models\Trajet;
use App\Utils\Response;
use App\Utils\Validator;

class TrajetController {
    public function index(): void {
        $filters = [
            'departure' => $_GET['departure'] ?? '',
            'destination' => $_GET['destination'] ?? '',
            'date' => $_GET['date'] ?? '',
            'available' => isset($_GET['available']) ? (bool)$_GET['available'] : false
        ];
        
        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $perPage = isset($_GET['per_page']) ? max(1, (int)$_GET['per_page']) : 10;
        
        $trajetModel = new Trajet();
        $total = $trajetModel->countAll($filters);
        $data = $trajetModel->findAll($filters, $page, $perPage);
        
        Response::paginated($data, $total, $page, $perPage);
    }
}
