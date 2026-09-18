<?php
namespace App\Utils;

class Response {
    public static function json(mixed $data, int $status = 200): void {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }

    public static function success(mixed $data, int $status = 200): void {
        self::json(['success' => true, 'data' => $data], $status);
    }

    public static function error(string $code, string $message, int $status = 400): void {
        self::json([
            'success' => false,
            'error' => [
                'code' => $code,
                'message' => $message,
            ]
        ], $status);
    }

    public static function paginated(array $data, int $total, int $page, int $perPage): void {
        self::json([
            'success' => true,
            'data' => $data,
            'current_page' => $page,
            'meta' => [
                'total' => $total,
                'page' => $page,
                'per_page' => $perPage,
                'total_pages' => max(1, (int) ceil($total / $perPage))
            ]
        ]);
    }
}
