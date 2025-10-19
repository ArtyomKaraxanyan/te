<?php

namespace Tests;

use App\Model\Order;
use PHPUnit\Framework\TestCase;

class ModelTest extends TestCase
{
    public function testOrderModel(): void
    {
        $order = new Order();
        $order->setId(1);
        $order->setDate('2024-01-01 10:00:00');
        $order->setCustomerName('John Doe');
        $order->setCustomerEmail('john@example.com');
        $order->setAddress('123 Main St');
        $order->setTotalAmount(299.99);
        $order->setItems('Product A, Product B');
        $order->setStatus('completed');

        $this->assertEquals(1, $order->getId());
        $this->assertEquals('2024-01-01 10:00:00', $order->getDate());
        $this->assertEquals('John Doe', $order->getCustomerName());
        $this->assertEquals('john@example.com', $order->getCustomerEmail());
        $this->assertEquals('123 Main St', $order->getAddress());
        $this->assertEquals(299.99, $order->getTotalAmount());
        $this->assertEquals('Product A, Product B', $order->getItems());
        $this->assertEquals('completed', $order->getStatus());
    }

    public function testOrderToArray(): void
    {
        $order = new Order();
        $order->setId(1);
        $order->setDate('2024-01-01 10:00:00');
        $order->setCustomerName('John Doe');
        $order->setCustomerEmail('john@example.com');
        $order->setAddress('123 Main St');
        $order->setTotalAmount(299.99);
        $order->setItems('Product A, Product B');
        $order->setStatus('completed');

        $array = $order->toArray();

        $this->assertIsArray($array);
        $this->assertEquals(1, $array['id']);
        $this->assertEquals('John Doe', $array['customer_name']);
        $this->assertEquals('john@example.com', $array['customer_email']);
    }

    public function testOrderFromArray(): void
    {
        $data = [
            'id' => 1,
            'date' => '2024-01-01 10:00:00',
            'customer_name' => 'John Doe',
            'customer_email' => 'john@example.com',
            'address' => '123 Main St',
            'total_amount' => 299.99,
            'items' => 'Product A, Product B',
            'status' => 'completed',
        ];

        $order = Order::fromArray($data);

        $this->assertEquals(1, $order->getId());
        $this->assertEquals('John Doe', $order->getCustomerName());
        $this->assertEquals('john@example.com', $order->getCustomerEmail());
        $this->assertEquals(299.99, $order->getTotalAmount());
    }
}
