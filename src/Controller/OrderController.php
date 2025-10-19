<?php

namespace App\Controller;

use App\Repository\OrderRepository;
use App\Service\ManticoreSearch;
use App\Service\Validator;

class OrderController
{
    private OrderRepository $repository;
    private ?ManticoreSearch $search = null;

    public function __construct()
    {
        $this->repository = new OrderRepository();
    }

    private function getSearch(): ManticoreSearch
    {
        if ($this->search === null) {
            $this->search = new ManticoreSearch();
        }
        return $this->search;
    }

    public function getStatistics(): void
    {
        $page = (int)($_GET['page'] ?? 1);
        $perPage = (int)($_GET['per_page'] ?? 10);
        $groupBy = $_GET['group_by'] ?? 'month';

        if ($page < 1) $page = 1;
        if ($perPage < 1 || $perPage > 100) $perPage = 10;

        if (!in_array($groupBy, ['day', 'month', 'year'])) {
            $this->jsonResponse([
                'error' => 'Invalid group_by parameter. Allowed values: day, month, year'
            ], 400);
            return;
        }

        $data = $this->repository->getStatisticsByPeriod($groupBy, $page, $perPage);
        $totalCount = $this->repository->getStatisticsCount($groupBy);
        $totalPages = ceil($totalCount / $perPage);

        $this->jsonResponse([
            'data' => $data,
            'pagination' => [
                'page' => $page,
                'per_page' => $perPage,
                'total_items' => $totalCount,
                'total_pages' => $totalPages,
            ],
            'group_by' => $groupBy,
        ]);
    }

    public function getOrder(): void
    {
        $id = (int)($_GET['id'] ?? 0);

        if (!Validator::validateId($id)) {
            $this->jsonResponse([
                'error' => 'Invalid order ID'
            ], 400);
            return;
        }

        $order = $this->repository->findById($id);

        if ($order === null) {
            $this->jsonResponse([
                'error' => 'Order not found'
            ], 404);
            return;
        }

        $this->jsonResponse($order->toArray());
    }

    public function search(): void
    {
        $query = Validator::sanitizeString($_GET['q'] ?? '', 255);
        $page = (int)($_GET['page'] ?? 1);
        $perPage = (int)($_GET['per_page'] ?? 10);

        if ($page < 1) $page = 1;
        if ($perPage < 1 || $perPage > 100) $perPage = 10;

        $offset = ($page - 1) * $perPage;

        try {
            $results = $this->getSearch()->search($query, $perPage, $offset);
        } catch (\Exception $e) {
            $results = $this->searchFallback($query, $perPage, $offset);
        }

        $this->jsonResponse([
            'query' => $query,
            'results' => $results,
            'pagination' => [
                'page' => $page,
                'per_page' => $perPage,
            ],
        ]);
    }

    private function searchFallback(string $query, int $limit, int $offset): array
    {
        if (empty($query)) {
            return [];
        }

        $orders = $this->repository->findAll($limit, $offset);
        return array_filter(array_map(function($order) use ($query) {
            $data = $order->toArray();
            $searchable = implode(' ', [
                $data['customer_name'] ?? '',
                $data['customer_email'] ?? '',
                $data['items'] ?? '',
            ]);

            if (stripos($searchable, $query) !== false) {
                return $data;
            }
            return null;
        }, $orders));
    }

    private function jsonResponse(array $data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }
}
