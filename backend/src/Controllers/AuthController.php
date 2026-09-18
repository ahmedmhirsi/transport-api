<?php
namespace App\Controllers;

use App\Models\User;
use App\Services\JWTService;
use App\Utils\Response;
use App\Utils\Validator;
use App\Utils\Logger;
use App\Config\Env;

class AuthController {
    public function login(): void {
        $data = json_decode(file_get_contents('php://input'), true) ?? [];

        $errors = Validator::validate($data, [
            'email' => 'required|email',
            'password' => 'required|string'
        ]);

        if (!empty($errors)) {
            Response::error('VALIDATION_ERROR', 'Invalid data provided', 422);
            return;
        }

        $userModel = new User();
        $user = $userModel->findByEmail($data['email']);

        if (!$user || !$userModel->verifyPassword($data['password'], $user['password'])) {
            Logger::warning('Failed login attempt for email: ' . $data['email']);
            Response::error('UNAUTHORIZED', 'Invalid credentials', 401);
            return;
        }

        $token = JWTService::encode([
            'user_id' => $user['id'],
            'email' => $user['email'],
            'role' => $user['role']
        ]);

        Logger::info('User logged in: ' . $user['id']);

        Response::json([
            'success' => true,
            'token' => $token,
            'expires_in' => (int) Env::get('JWT_EXPIRATION', '3600'),
            'user' => [
                'id' => $user['id'],
                'name' => $user['name'] ?? null,
                'email' => $user['email'],
                'role' => $user['role']
            ]
        ]);
    }
}
