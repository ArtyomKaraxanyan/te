<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Service\ManticoreIndexer;

echo "Starting Manticore reindex...\n";

try {
    $indexer = new ManticoreIndexer();
    $count = $indexer->indexAllOrders();
    echo "Successfully indexed {$count} orders\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
