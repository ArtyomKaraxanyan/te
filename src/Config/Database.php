<?php

namespace App\Config;

use PDO;
use PDOException;

class Database
{
    private static ?PDO $connection = null;

    public static function getConnection(): PDO
    {
        if (self::$connection === null) {
            $host = $_ENV['DB_HOST'] ?? getenv('DB_HOST') ?? 'mysql';
            $port = $_ENV['DB_PORT'] ?? getenv('DB_PORT') ?? '3306';
            $dbname = $_ENV['DB_NAME'] ?? getenv('DB_NAME') ?? 'tile_app';
            $username = $_ENV['DB_USER'] ?? getenv('DB_USER') ?? 'root';
            $password = $_ENV['DB_PASS'] ?? getenv('DB_PASS') ?? 'root';

            try {
                $dsn = "mysql:host={$host};port={$port};dbname={$dbname};charset=utf8mb4";
                self::$connection = new PDO($dsn, $username, $password, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]);
            } catch (PDOException $e) {
                throw new \RuntimeException("Database connection failed: " . $e->getMessage());
            }
        }

        return self::$connection;
    }

    public static function closeConnection(): void
    {
        self::$connection = null;
    }
}
