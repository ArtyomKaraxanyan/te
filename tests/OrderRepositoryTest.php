<?php

namespace Tests;

use App\Model\Order;
use App\Repository\OrderRepository;
use PHPUnit\Framework\TestCase;

class OrderRepositoryTest extends TestCase
{
    private OrderRepository $repository;

    protected function setUp(): void
    {
        $this->repository = new OrderRepository();
    }

    public function testFindById(): void
    {
        $order = $this->repository->findById(1);
        $this->assertNotNull($order);
        $this->assertInstanceOf(Order::class, $order);
        $this->assertEquals(1, $order->getId());
    }

    public function testFindAll(): void
    {
        $orders = $this->repository->findAll(5, 0);
        $this->assertIsArray($orders);
        $this->assertLessThanOrEqual(5, count($orders));

        foreach ($orders as $order) {
            $this->assertInstanceOf(Order::class, $order);
        }
    }

    public function testCount(): void
    {
        $count = $this->repository->count();
        $this->assertIsInt($count);
        $this->assertGreaterThanOrEqual(0, $count);
    }

    public function testGetStatisticsByPeriod(): void
    {
        $statistics = $this->repository->getStatisticsByPeriod('month', 1, 10);
        $this->assertIsArray($statistics);

        foreach ($statistics as $stat) {
            $this->assertArrayHasKey('period', $stat);
            $this->assertArrayHasKey('count', $stat);
        }
    }

    public function testSaveNewOrder(): void
    {
        $order = new Order();
        $order->setDate(date('Y-m-d H:i:s'));
        $order->setCustomerName('Test Customer');
        $order->setCustomerEmail('test@example.com');
        $order->setAddress('Test Address');
        $order->setTotalAmount(99.99);
        $order->setItems('Test Items');
        $order->setStatus('pending');

        $savedOrder = $this->repository->save($order);

        $this->assertNotNull($savedOrder->getId());
        $this->assertGreaterThan(0, $savedOrder->getId());
        $this->assertEquals('Test Customer', $savedOrder->getCustomerName());
    }
}
