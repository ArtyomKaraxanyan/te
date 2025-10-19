<?php

namespace App\Repository;

use App\Config\Database;
use App\Model\Order;
use PDO;

class OrderRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function findById(int $id): ?Order
    {
        $stmt = $this->db->prepare("SELECT * FROM orders WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $data = $stmt->fetch();

        return $data ? Order::fromArray($data) : null;
    }

    public function findAll(int $limit = 10, int $offset = 0): array
    {
        $stmt = $this->db->prepare("SELECT * FROM orders LIMIT :limit OFFSET :offset");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        $orders = [];
        while ($data = $stmt->fetch()) {
            $orders[] = Order::fromArray($data);
        }

        return $orders;
    }

    public function count(): int
    {
        $stmt = $this->db->query("SELECT COUNT(*) as count FROM orders");
        $result = $stmt->fetch();
        return (int)$result['count'];
    }

    public function save(Order $order): Order
    {
        if ($order->getId() === null) {
            return $this->insert($order);
        }
        return $this->update($order);
    }

    private function insert(Order $order): Order
    {
        $stmt = $this->db->prepare(
            "INSERT INTO orders (date, customer_name, customer_email, address, total_amount, items, status)
             VALUES (:date, :customer_name, :customer_email, :address, :total_amount, :items, :status)"
        );

        $stmt->execute([
            'date' => $order->getDate(),
            'customer_name' => $order->getCustomerName(),
            'customer_email' => $order->getCustomerEmail(),
            'address' => $order->getAddress(),
            'total_amount' => $order->getTotalAmount(),
            'items' => $order->getItems(),
            'status' => $order->getStatus(),
        ]);

        $order->setId((int)$this->db->lastInsertId());
        return $order;
    }

    private function update(Order $order): Order
    {
        $stmt = $this->db->prepare(
            "UPDATE orders SET
                date = :date,
                customer_name = :customer_name,
                customer_email = :customer_email,
                address = :address,
                total_amount = :total_amount,
                items = :items,
                status = :status
             WHERE id = :id"
        );

        $stmt->execute([
            'id' => $order->getId(),
            'date' => $order->getDate(),
            'customer_name' => $order->getCustomerName(),
            'customer_email' => $order->getCustomerEmail(),
            'address' => $order->getAddress(),
            'total_amount' => $order->getTotalAmount(),
            'items' => $order->getItems(),
            'status' => $order->getStatus(),
        ]);

        return $order;
    }

    public function getStatisticsByPeriod(string $groupBy, int $page, int $perPage): array
    {
        $offset = ($page - 1) * $perPage;

        $groupByField = match ($groupBy) {
            'day' => 'DATE(date)',
            'month' => 'DATE_FORMAT(date, "%Y-%m")',
            'year' => 'YEAR(date)',
            default => 'DATE(date)',
        };

        $stmt = $this->db->prepare(
            "SELECT {$groupByField} as period, COUNT(*) as count
             FROM orders
             GROUP BY period
             ORDER BY period DESC
             LIMIT :limit OFFSET :offset"
        );

        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function getStatisticsCount(string $groupBy): int
    {
        $groupByField = match ($groupBy) {
            'day' => 'DATE(date)',
            'month' => 'DATE_FORMAT(date, "%Y-%m")',
            'year' => 'YEAR(date)',
            default => 'DATE(date)',
        };

        $stmt = $this->db->query(
            "SELECT COUNT(DISTINCT {$groupByField}) as count FROM orders"
        );

        $result = $stmt->fetch();
        return (int)$result['count'];
    }
}
