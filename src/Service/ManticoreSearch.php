<?php

namespace App\Service;

use PDO;
use PDOException;

class ManticoreSearch
{
    private ?PDO $connection = null;

    public function __construct()
    {
        $this->connect();
        $this->ensureIndexExists();
    }

    private function ensureIndexExists(): void
    {
        try {
            $this->connection->query("SHOW TABLES LIKE 'orders'");
        } catch (\PDOException $e) {
            $this->connection->exec("
                CREATE TABLE IF NOT EXISTS orders (
                    order_id integer,
                    customer_name text,
                    customer_email text,
                    address text,
                    items text,
                    created_at timestamp,
                    total_amount float,
                    status string
                )
            ");
        }
    }

    private function connect(): void
    {
        $host = $_ENV['MANTICORE_HOST'] ?? getenv('MANTICORE_HOST') ?? 'manticore';
        $port = $_ENV['MANTICORE_PORT'] ?? getenv('MANTICORE_PORT') ?? '9306';

        try {
            $dsn = "mysql:host={$host};port={$port}";
            $this->connection = new PDO($dsn, '', '', [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
        } catch (PDOException $e) {
            error_log("Manticore connection failed: " . $e->getMessage());
            throw new \RuntimeException("Search engine connection failed");
        }
    }

    public function indexOrder(array $order): bool
    {
        try {
            $stmt = $this->connection->prepare(
                "INSERT INTO orders (id, order_id, customer_name, customer_email, address, items, created_at, total_amount, status)
                 VALUES (:id, :order_id, :customer_name, :customer_email, :address, :items, :created_at, :total_amount, :status)"
            );

            return $stmt->execute([
                'id' => $order['id'],
                'order_id' => $order['id'],
                'customer_name' => $order['customer_name'] ?? '',
                'customer_email' => $order['customer_email'] ?? '',
                'address' => $order['address'] ?? '',
                'items' => $order['items'] ?? '',
                'created_at' => strtotime($order['date'] ?? 'now'),
                'total_amount' => (float)($order['total_amount'] ?? 0),
                'status' => $order['status'] ?? 'pending',
            ]);
        } catch (PDOException $e) {
            error_log("Manticore indexing failed: " . $e->getMessage());
            return false;
        }
    }

    public function search(string $query, int $limit = 10, int $offset = 0): array
    {
        try {
            $stmt = $this->connection->prepare(
                "SELECT * FROM orders WHERE MATCH(:query) LIMIT :limit OFFSET :offset"
            );

            $stmt->bindValue(':query', $query, PDO::PARAM_STR);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Manticore search failed: " . $e->getMessage());
            return [];
        }
    }
}
