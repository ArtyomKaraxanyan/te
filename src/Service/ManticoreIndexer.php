<?php

namespace App\Service;

use App\Repository\OrderRepository;

class ManticoreIndexer
{
    private ManticoreSearch $search;
    private OrderRepository $repository;

    public function __construct()
    {
        $this->search = new ManticoreSearch();
        $this->repository = new OrderRepository();
    }

    public function indexAllOrders(): int
    {
        $orders = $this->repository->findAll(1000, 0);
        $count = 0;

        foreach ($orders as $order) {
            try {
                $this->search->indexOrder($order->toArray());
                $count++;
            } catch (\Exception $e) {
                error_log("Failed to index order {$order->getId()}: " . $e->getMessage());
            }
        }

        return $count;
    }
}
