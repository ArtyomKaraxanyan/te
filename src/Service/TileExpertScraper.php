<?php

namespace App\Service;

class TileExpertScraper
{
    private const BASE_URL = 'https://tile.expert';

    public function getPrice(string $factory, string $collection, string $article): ?array
    {
        $url = sprintf(
            '%s/fr/tile/%s/%s/a/%s',
            self::BASE_URL,
            $factory,
            $collection,
            $article
        );

        try {
            $html = $this->fetchUrl($url);
            $price = $this->extractPrice($html);

            if ($price === null) {
                return null;
            }

            return [
                'price' => $price,
                'factory' => $factory,
                'collection' => $collection,
                'article' => $article,
            ];
        } catch (\Exception $e) {
            error_log("Error scraping tile.expert: " . $e->getMessage());
            return null;
        }
    }

    private function fetchUrl(string $url): string
    {
        $ch = curl_init();

        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS => 5,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
            CURLOPT_HTTPHEADER => [
                'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                'Accept-Language: en-US,en;q=0.9',
            ],
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);

        curl_close($ch);

        if ($response === false) {
            throw new \RuntimeException("cURL error: {$error}");
        }

        if ($httpCode !== 200) {
            throw new \RuntimeException("HTTP error: {$httpCode}");
        }

        return $response;
    }

    private function extractPrice(string $html): ?float
    {
        $patterns = [
            '/<meta[^>]+property="product:price:amount"[^>]+content="([0-9,.]+)"/',
            '/"price"\s*:\s*"?([0-9,.]+)"?/',
            '/<(?:span|div)[^>]*class="[^"]*price[^"]*"[^>]*>(?:[^€]*)?([0-9,.]+)\s*€/i',
            '/([0-9]+(?:[,.][0-9]{2})?)\s*€/',
            '/data-price="([0-9,.]+)"/',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $html, $matches)) {
                $priceStr = $matches[1];
                $priceStr = str_replace(',', '.', $priceStr);
                $priceStr = str_replace(' ', '', $priceStr);
                $price = (float)$priceStr;

                if ($price > 0) {
                    return $price;
                }
            }
        }

        return null;
    }
}
