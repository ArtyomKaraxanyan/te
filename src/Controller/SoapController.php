<?php

namespace App\Controller;

use App\Model\Order;
use App\Repository\OrderRepository;
use App\Service\ManticoreSearch;
use App\Service\Validator;
use SoapServer;

class SoapController
{
    private OrderRepository $repository;

    public function __construct()
    {
        $this->repository = new OrderRepository();
    }

    public function serve(): void
    {
        $wsdl = __DIR__ . '/../../config/order.wsdl';

        if (!file_exists($wsdl)) {
            http_response_code(500);
            echo "WSDL file not found";
            exit;
        }

        if (isset($_GET['wsdl'])) {
            header('Content-Type: text/xml; charset=utf-8');
            readfile($wsdl);
            exit;
        }

        $server = new SoapServer($wsdl, [
            'uri' => 'http://localhost:8080/soap',
            'encoding' => 'UTF-8',
        ]);

        $server->setObject($this);
        $server->handle();
    }

    public function createOrder($params): array
    {
        try {
            if (is_object($params)) {
                $customerName = $params->customerName ?? '';
                $customerEmail = $params->customerEmail ?? '';
                $address = $params->address ?? '';
                $totalAmount = (float)($params->totalAmount ?? 0);
                $items = $params->items ?? '';
                $status = $params->status ?? 'pending';
            } else {
                $customerName = func_get_arg(0);
                $customerEmail = func_get_arg(1);
                $address = func_get_arg(2);
                $totalAmount = func_get_arg(3);
                $items = func_get_arg(4);
                $status = func_get_arg(5) ?? 'pending';
            }
            if (empty($customerName) || empty($customerEmail) || empty($address)) {
                return [
                    'success' => false,
                    'error' => 'Required fields missing'
                ];
            }

            if (!Validator::validateEmail($customerEmail)) {
                return [
                    'success' => false,
                    'error' => 'Invalid email address'
                ];
            }

            if (!Validator::validatePositiveFloat($totalAmount)) {
                return [
                    'success' => false,
                    'error' => 'Total amount must be positive'
                ];
            }

            $customerName = Validator::sanitizeString($customerName);
            $customerEmail = Validator::sanitizeString($customerEmail);
            $address = Validator::sanitizeString($address, 500);
            $items = Validator::sanitizeString($items, 1000);
            $status = Validator::sanitizeString($status, 50);

            $order = new Order();
            $order->setDate(date('Y-m-d H:i:s'));
            $order->setCustomerName($customerName);
            $order->setCustomerEmail($customerEmail);
            $order->setAddress($address);
            $order->setTotalAmount($totalAmount);
            $order->setItems($items);
            $order->setStatus($status);

            $savedOrder = $this->repository->save($order);

            try {
                $search = new ManticoreSearch();
                $search->indexOrder($savedOrder->toArray());
            } catch (\Exception $e) {
                error_log("Manticore indexing failed: " . $e->getMessage());
            }

            return [
                'success' => true,
                'order_id' => $savedOrder->getId(),
                'message' => 'Order created successfully',
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }
}
