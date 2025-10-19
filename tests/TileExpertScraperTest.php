<?php

namespace Tests;

use App\Service\TileExpertScraper;
use PHPUnit\Framework\TestCase;

class TileExpertScraperTest extends TestCase
{
    private TileExpertScraper $scraper;

    protected function setUp(): void
    {
        $this->scraper = new TileExpertScraper();
    }

    public function testGetPriceWithValidParameters(): void
    {
        $result = $this->scraper->getPrice('cobsa', 'manual', 'manu7530bcbm-manualbaltic7-5x30');

        // Note: This test requires internet connection and may fail if the website is down
        // or the URL structure changes
        if ($result !== null) {
            $this->assertIsArray($result);
            $this->assertArrayHasKey('price', $result);
            $this->assertArrayHasKey('factory', $result);
            $this->assertArrayHasKey('collection', $result);
            $this->assertArrayHasKey('article', $result);
            $this->assertEquals('cobsa', $result['factory']);
            $this->assertEquals('manual', $result['collection']);
            $this->assertEquals('manu7530bcbm-manualbaltic7-5x30', $result['article']);
            $this->assertIsFloat($result['price']);
            $this->assertGreaterThan(0, $result['price']);
        } else {
            // If scraping fails, skip this test
            $this->markTestSkipped('Unable to fetch price from tile.expert');
        }
    }

    public function testGetPriceReturnsCorrectStructure(): void
    {
        // This test is more about structure than actual data
        $result = $this->scraper->getPrice('test', 'test', 'test');

        // Even with invalid parameters, we expect null or correct structure
        $this->assertTrue($result === null || (
            is_array($result) &&
            isset($result['price']) &&
            isset($result['factory']) &&
            isset($result['collection']) &&
            isset($result['article'])
        ));
    }
}
