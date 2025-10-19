<?php

namespace App\Model;

class Order
{
    private ?int $id = null;
    private ?string $date = null;
    private ?string $customerName = null;
    private ?string $customerEmail = null;
    private ?string $address = null;
    private ?float $totalAmount = null;
    private ?string $items = null;
    private string $status = 'pending';

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getDate(): ?string
    {
        return $this->date;
    }

    public function setDate(string $date): self
    {
        $this->date = $date;
        return $this;
    }

    public function getCustomerName(): ?string
    {
        return $this->customerName;
    }

    public function setCustomerName(string $customerName): self
    {
        $this->customerName = $customerName;
        return $this;
    }

    public function getCustomerEmail(): ?string
    {
        return $this->customerEmail;
    }

    public function setCustomerEmail(string $customerEmail): self
    {
        $this->customerEmail = $customerEmail;
        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): self
    {
        $this->address = $address;
        return $this;
    }

    public function getTotalAmount(): ?float
    {
        return $this->totalAmount;
    }

    public function setTotalAmount(float $totalAmount): self
    {
        $this->totalAmount = $totalAmount;
        return $this;
    }

    public function getItems(): ?string
    {
        return $this->items;
    }

    public function setItems(string $items): self
    {
        $this->items = $items;
        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;
        return $this;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'date' => $this->date,
            'customer_name' => $this->customerName,
            'customer_email' => $this->customerEmail,
            'address' => $this->address,
            'total_amount' => $this->totalAmount,
            'items' => $this->items,
            'status' => $this->status,
        ];
    }

    public static function fromArray(array $data): self
    {
        $order = new self();
        if (isset($data['id'])) $order->setId((int)$data['id']);
        if (isset($data['date'])) $order->setDate($data['date']);
        if (isset($data['customer_name'])) $order->setCustomerName($data['customer_name']);
        if (isset($data['customer_email'])) $order->setCustomerEmail($data['customer_email']);
        if (isset($data['address'])) $order->setAddress($data['address']);
        if (isset($data['total_amount'])) $order->setTotalAmount((float)$data['total_amount']);
        if (isset($data['items'])) $order->setItems($data['items']);
        if (isset($data['status'])) $order->setStatus($data['status']);
        return $order;
    }
}
