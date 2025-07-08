<?php
require_once '../database/connection.php';
header('Content-Type: application/json');

try {
    $action = $_GET['action'] ?? 'all';
    
    switch ($action) {
        case 'all':
            $products = fetchAll("SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id ORDER BY p.created_at DESC");
            echo json_encode(['status' => 'success', 'products' => $products]);
            break;
            
        case 'by_category':
            $category = $_GET['category'] ?? '';
            if ($category) {
                $products = fetchAll(
                    "SELECT p.*, c.name as category_name FROM products p 
                     LEFT JOIN categories c ON p.category_id = c.id 
                     WHERE c.name = ? ORDER BY p.created_at DESC", 
                    [$category]
                );
                echo json_encode(['status' => 'success', 'products' => $products]);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Category required']);
            }
            break;
            
        case 'search':
            $query = $_GET['q'] ?? '';
            if ($query) {
                $searchTerm = '%' . $query . '%';
                $products = fetchAll(
                    "SELECT p.*, c.name as category_name FROM products p 
                     LEFT JOIN categories c ON p.category_id = c.id 
                     WHERE p.name LIKE ? OR p.description LIKE ? 
                     ORDER BY p.created_at DESC", 
                    [$searchTerm, $searchTerm]
                );
                echo json_encode(['status' => 'success', 'products' => $products]);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Search query required']);
            }
            break;
            
        default:
            echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
            break;
    }
    
} catch (Exception $e) {
    error_log("Products API error: " . $e->getMessage());
    echo json_encode(['status' => 'error', 'message' => 'Failed to fetch products']);
}
?>