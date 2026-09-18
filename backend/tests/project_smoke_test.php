<?php
require_once __DIR__ . '/../src/Utils/Autoloader.php';
\App\Utils\Autoloader::register();

use App\Services\JWTService;
use App\Models\User;

$tests = 0;
$failures = 0;

$assert = function (bool $condition, string $message) use (&$tests, &$failures): void {
    $tests++;
    if (!$condition) {
        $failures++;
        fwrite(STDERR, "FAIL: $message\n");
    }
};

$demoHash = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi';
$assert(password_verify('password', $demoHash), 'Password verification should work for seeded demo users');

$token = JWTService::encode([
    'user_id' => 42,
    'email' => 'admin@transport.tn',
    'role' => 'admin',
]);
$decoded = JWTService::decode($token);
$assert(is_array($decoded) && $decoded['user_id'] === 42 && $decoded['email'] === 'admin@transport.tn', 'JWT should encode and decode payload correctly');

$assert((hash('sha256', 'tk_live_test_key_travel_agency_2026') === 'f79850ef0918d2a3cd78f249c85860aa3b4ca146269bb2bb416d0ae6ef01863c'), 'Demo API key hash should match the database seed');

$userModel = new User();
$assert($userModel->verifyPassword('password', $demoHash), 'User model should verify password against hashed password');

if ($failures > 0) {
    fwrite(STDERR, "Smoke tests failed: $failures/$tests\n");
    exit(1);
}

fwrite(STDOUT, "Smoke tests passed: $tests assertions\n");
