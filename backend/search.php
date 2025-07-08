<?php
header('Content-Type: application/json');

// Sample product database (in a real app, this would be from a database)
$products = [
    ['name' => 'Air Jordan 1 Low Dunk Green', 'price' => 150, 'category' => 'featured'],
    ['name' => 'Nike Dunk Low Off-White Lot 1', 'price' => 180, 'category' => 'featured'],
    ['name' => 'Air Jordan 1 High OG \'Bloodline\'', 'price' => 200, 'category' => 'featured'],
    ['name' => 'Travis Scott x Air Jordan 1', 'price' => 220, 'category' => 'featured'],
    ['name' => 'Men\'s Air Max Runner', 'price' => 120, 'category' => 'men'],
    ['name' => 'Men\'s Classic High Top', 'price' => 140, 'category' => 'men'],
    ['name' => 'Men\'s Sport Pro', 'price' => 160, 'category' => 'men'],
    ['name' => 'Women\'s Stylish Slip-On', 'price' => 110, 'category' => 'women'],
    ['name' => 'Women\'s Air Boost', 'price' => 120, 'category' => 'women'],
    ['name' => 'Women\'s Elegance Heel', 'price' => 135, 'category' => 'women'],
    ['name' => 'Kids Classic Runner', 'price' => 90, 'category' => 'kids'],
    ['name' => 'Mini Sports Pro', 'price' => 95, 'category' => 'kids'],
    ['name' => 'Kids Adventure Boot', 'price' => 105, 'category' => 'kids'],
    ['name' => 'Discounted Runner', 'price' => 120, 'category' => 'sale'],
    ['name' => 'Clearance Slip-On', 'price' => 75, 'category' => 'sale'],
    ['name' => 'End of Season Boot', 'price' => 135, 'category' => 'sale'],
];

if (isset($_POST['query'])) {
    $query = strtolower(trim($_POST['query']));
    
    if (strlen($query) < 2) {
        echo json_encode(['status' => 'error', 'message' => 'Query too short']);
        exit;
    }
    
    $results = [];
    
    foreach ($products as $product) {
        $productName = strtolower($product['name']);
        
        // Search in product name
        if (strpos($productName, $query) !== false) {
            $results[] = $product;
        }
        // Search by category
        elseif (strpos($product['category'], $query) !== false) {
            $results[] = $product;
        }
        // Search by price range
        elseif (is_numeric($query) && abs($product['price'] - floatval($query)) <= 20) {
            $results[] = $product;
        }
    }
    
    // Remove duplicates and limit results
    $results = array_unique($results, SORT_REGULAR);
    $results = array_slice($results, 0, 8); // Limit to 8 results
    
    if (empty($results)) {
        echo json_encode(['status' => 'no_results', 'message' => 'No products found']);
    } else {
        echo json_encode(['status' => 'success', 'results' => array_values($results)]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'No query provided']);
}
?>
