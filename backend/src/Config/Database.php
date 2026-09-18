<?php
namespace App\Config;

use PDO;
use PDOException;
use App\Utils\Logger;
use App\Utils\Response;

class Database {
    private static ?PDO $instance = null;

    public static function getInstance(): PDO {
        if (self::$instance === null) {
            try {
                $host = Env::get('DB_HOST', '127.0.0.1');
                $port = Env::get('DB_PORT', '3306');
                $db   = Env::get('DB_DATABASE', 'reservation_api');
                $user = Env::get('DB_USERNAME', 'root');
                $pass = Env::get('DB_PASSWORD', '');
                
                $dsn = "mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4";
                
                self::$instance = new PDO($dsn, $user, $pass, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]);
            } catch (PDOException $e) {
                Logger::error('Database connection failed: ' . $e->getMessage());
                Response::error('DB_ERROR', 'Database connection failed', 500);
                exit;
            }
        }
        return self::$instance;
    }
}
