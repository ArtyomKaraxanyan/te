<?php

namespace App\Controller;

use App\Service\TileExpertScraper;
use App\Service\Validator;

class PriceController
{
    private TileExpertScraper $scraper;

    public function __construct()
    {
        $this->scraper = new TileExpertScraper();
    }

    public function getPrice(): void
    {
        $factory = $_GET['factory'] ?? null;
        $collection = $_GET['collection'] ?? null;
        $article = $_GET['article'] ?? null;

        if (!$factory || !$collection || !$article) {
            $this->jsonResponse([
                'error' => 'Missing required parameters: factory, collection, article'
            ], 400);
            return;
        }

        $factory = Validator::sanitizeString($factory, 100);
        $collection = Validator::sanitizeString($collection, 100);
        $article = Validator::sanitizeString($article, 255);

        $result = $this->scraper->getPrice($factory, $collection, $article);

        if ($result === null) {
            $this->jsonResponse([
                'error' => 'Unable to fetch price from tile.expert'
            ], 404);
            return;
        }

        $this->jsonResponse($result);
    }

    private function jsonResponse(array $data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }
}
