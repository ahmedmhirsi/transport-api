<?php

// Error reporting for development
error_reporting(E_ALL);
ini_set('display_errors', '0');

// Load autoloader
require_once __DIR__ . '/../src/Utils/Autoloader.php';
\App\Utils\Autoloader::register();

// Load environment
use App\Config\Env;
$envPath = __DIR__ . '/../.env';
if (!file_exists($envPath)) {
    $envPath = __DIR__ . '/../../.env';
}
Env::load($envPath);

// Set error handler
set_exception_handler(function (\Throwable $e) {
    \App\Utils\Logger::error('Uncaught exception: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
    \App\Utils\Response::error('INTERNAL_ERROR', 'Internal server error', 500);
});

// Handle request
require_once __DIR__ . '/../routes/api.php';
\App\Router::handle();
