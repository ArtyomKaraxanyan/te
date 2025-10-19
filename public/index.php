<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Controller\PriceController;
use App\Controller\OrderController;
use App\Controller\SoapController;

$isProduction = ($_ENV['APP_ENV'] ?? getenv('APP_ENV')) === 'production';
if (!$isProduction) {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(0);
    ini_set('display_errors', '0');
    ini_set('log_errors', '1');
}

$allowedOrigins = ['http://localhost:8080', 'http://localhost:3000'];
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
if (in_array($origin, $allowedOrigins) || !$isProduction) {
    header("Access-Control-Allow-Origin: $origin");
}
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

if ($isProduction) {
    $clientIp = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    if (!\App\Service\RateLimiter::check($clientIp)) {
        http_response_code(429);
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Rate limit exceeded. Try again later.']);
        exit;
    }
}

$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$requestMethod = $_SERVER['REQUEST_METHOD'];

try {
    switch ($requestUri) {
        case '/api/price':
            if ($requestMethod === 'GET') {
                $controller = new PriceController();
                $controller->getPrice();
            }
            break;
        case '/api/orders/statistics':
            if ($requestMethod === 'GET') {
                $controller = new OrderController();
                $controller->getStatistics();
            }
            break;
        case '/api/orders/new':
            $controller = new SoapController();
            $controller->serve();
            break;
        case '/api/orders':
            if ($requestMethod === 'GET') {
                $controller = new OrderController();
                $controller->getOrder();
            }
            break;
        case '/api/orders/search':
            if ($requestMethod === 'GET') {
                $controller = new OrderController();
                $controller->search();
            }
            break;
        case '/swagger':
            readfile(__DIR__ . '/swagger.html');
            exit;
        case '/swagger.yaml':
            header('Content-Type: application/x-yaml');
            readfile(__DIR__ . '/swagger.yaml');
            exit;
        case '/':
        case '':
            header('Content-Type: application/json');
            echo json_encode([
                'name' => 'Tile Order Management API',
                'version' => '1.0.0',
                'endpoints' => [
                    [
                        'method' => 'GET',
                        'path' => '/api/price',
                        'description' => 'Get tile price from tile.expert',
                        'parameters' => [
                            'factory' => 'Factory name (required)',
                            'collection' => 'Collection name (required)',
                            'article' => 'Article number (required)',
                        ],
                        'example' => '/api/price?factory=cobsa&collection=manual&article=manu7530bcbm-manualbaltic7-5x30',
                    ],
                    [
                        'method' => 'GET',
                        'path' => '/api/orders/statistics',
                        'description' => 'Get order statistics with pagination and grouping',
                        'parameters' => [
                            'page' => 'Page number (default: 1)',
                            'per_page' => 'Items per page (default: 10, max: 100)',
                            'group_by' => 'Group by: day, month, year (default: month)',
                        ],
                        'example' => '/api/orders/statistics?page=1&per_page=10&group_by=month',
                    ],
                    [
                        'method' => 'POST',
                        'path' => '/soap',
                        'description' => 'SOAP service for creating orders',
                        'wsdl' => '/soap?wsdl',
                    ],
                    [
                        'method' => 'GET',
                        'path' => '/api/orders',
                        'description' => 'Get single order by ID',
                        'parameters' => [
                            'id' => 'Order ID (required)',
                        ],
                        'example' => '/api/orders?id=1',
                    ],
                    [
                        'method' => 'GET',
                        'path' => '/api/orders/search',
                        'description' => 'Search orders using Manticore Search',
                        'parameters' => [
                            'q' => 'Search query',
                            'page' => 'Page number (default: 1)',
                            'per_page' => 'Items per page (default: 10)',
                        ],
                        'example' => '/api/orders/search?q=john&page=1&per_page=10',
                    ],
                ],
                'documentation' => 'See /swagger for OpenAPI documentation',
            ], JSON_PRETTY_PRINT);
            break;
        default:
            http_response_code(404);
            header('Content-Type: application/json');
            echo json_encode([
                'error' => 'Endpoint not found',
                'path' => $requestUri,
            ]);
            break;
    }
} catch (\Exception $e) {
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode([
        'error' => 'Internal server error',
        'message' => $e->getMessage(),
    ]);
}
