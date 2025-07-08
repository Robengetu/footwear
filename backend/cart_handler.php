<?php
session_start();
require_once '../database/connection.php';
header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Please log in first']);
    exit;
}

// Helper functions
function getCartItems($user_id) {
    return fetchAll(
        "SELECT c.id, c.quantity, p.name, p.price, p.original_price, p.is_sale 
         FROM cart c 
         JOIN products p ON c.product_id = p.id 
         WHERE c.user_id = ?", 
        [$user_id]
    );
}

function getTotalCartQuantity($user_id) {
    $result = fetchOne("SELECT SUM(quantity) as total FROM cart WHERE user_id = ?", [$user_id]);
    return $result['total'] ?? 0;
}

// Handle different actions
if (isset($_POST['action'])) {
    $action = $_POST['action'];

    switch ($action) {
        case 'add':
            $name = $_POST['name'] ?? '';
            $price = floatval($_POST['price'] ?? 0);
            $user_id = $_SESSION['user_id'];
            $buyNow = isset($_POST['buy_now']) && $_POST['buy_now'] == '1';

            if (empty($name) || $price <= 0) {
                echo json_encode(['status' => 'error', 'message' => 'Invalid product data']);
                exit;
            }

            try {
                // Find product by name
                $product = fetchOne("SELECT id FROM products WHERE name = ?", [$name]);
                if (!$product) {
                    echo json_encode(['status' => 'error', 'message' => 'Product not found']);
                    exit;
                }

                // Check if item already exists in cart
                $existingItem = fetchOne("SELECT id, quantity FROM cart WHERE user_id = ? AND product_id = ?", 
                                       [$user_id, $product['id']]);

                if ($existingItem) {
                    if ($buyNow) {
                        // For Buy Now, set quantity to 1
                        executeQuery("UPDATE cart SET quantity = 1 WHERE id = ?", [$existingItem['id']]);
                    } else {
                        // For Add to Cart, increment
                        executeQuery("UPDATE cart SET quantity = quantity + 1 WHERE id = ?", [$existingItem['id']]);
                    }
                } else {
                    // Add new item
                    executeQuery("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, 1)", 
                               [$user_id, $product['id']]);
                }

                // Get updated cart and total quantity
                $cart = getCartItems($user_id);
                $totalQty = getTotalCartQuantity($user_id);

                echo json_encode([
                    'status' => 'added',
                    'cart' => $cart,
                    'totalQty' => $totalQty
                ]);
            } catch (Exception $e) {
                error_log("Cart add error: " . $e->getMessage());
                echo json_encode(['status' => 'error', 'message' => 'Failed to add item to cart']);
            }
            break;

        case 'get_cart':
            try {
                $user_id = $_SESSION['user_id'];
                $cart = getCartItems($user_id);
                $totalQty = getTotalCartQuantity($user_id);
                
                echo json_encode([
                    'status' => 'success',
                    'cart' => $cart,
                    'totalQty' => $totalQty
                ]);
            } catch (Exception $e) {
                error_log("Get cart error: " . $e->getMessage());
                echo json_encode(['status' => 'error', 'message' => 'Failed to get cart']);
            }
            break;

        case 'clear':
            try {
                $user_id = $_SESSION['user_id'];
                executeQuery("DELETE FROM cart WHERE user_id = ?", [$user_id]);
                echo json_encode(['status' => 'cleared']);
            } catch (Exception $e) {
                error_log("Clear cart error: " . $e->getMessage());
                echo json_encode(['status' => 'error', 'message' => 'Failed to clear cart']);
            }
            break;

        case 'remove':
            $name = $_POST['name'] ?? '';
            if ($name) {
                try {
                    $user_id = $_SESSION['user_id'];
                    $product = fetchOne("SELECT id FROM products WHERE name = ?", [$name]);
                    
                    if ($product) {
                        executeQuery("DELETE FROM cart WHERE user_id = ? AND product_id = ?", 
                                   [$user_id, $product['id']]);
                        
                        $cart = getCartItems($user_id);
                        $totalQty = getTotalCartQuantity($user_id);
                        
                        echo json_encode([
                            'status' => 'removed',
                            'cart' => $cart,
                            'totalQty' => $totalQty
                        ]);
                    } else {
                        echo json_encode(['status' => 'error', 'message' => 'Product not found']);
                    }
                } catch (Exception $e) {
                    error_log("Remove cart item error: " . $e->getMessage());
                    echo json_encode(['status' => 'error', 'message' => 'Failed to remove item']);
                }
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Product name required']);
            }
            break;

        case 'update_quantity':
            $name = $_POST['name'] ?? '';
            $quantity = intval($_POST['quantity'] ?? 0);
            
            if ($name && $quantity > 0) {
                try {
                    $user_id = $_SESSION['user_id'];
                    $product = fetchOne("SELECT id FROM products WHERE name = ?", [$name]);
                    
                    if ($product) {
                        executeQuery("UPDATE cart SET quantity = ? WHERE user_id = ? AND product_id = ?", 
                                   [$quantity, $user_id, $product['id']]);
                        
                        $cart = getCartItems($user_id);
                        $totalQty = getTotalCartQuantity($user_id);
                        
                        echo json_encode([
                            'status' => 'updated',
                            'cart' => $cart,
                            'totalQty' => $totalQty
                        ]);
                    } else {
                        echo json_encode(['status' => 'error', 'message' => 'Product not found']);
                    }
                } catch (Exception $e) {
                    error_log("Update quantity error: " . $e->getMessage());
                    echo json_encode(['status' => 'error', 'message' => 'Failed to update quantity']);
                }
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Invalid quantity']);
            }
            break;

        case 'buy':
            try {
                $user_id = $_SESSION['user_id'];
                $cart = getCartItems($user_id);
                
                if (empty($cart)) {
                    echo json_encode(['status' => 'empty']);
                } else {
                    echo json_encode(['status' => 'success']);
                }
            } catch (Exception $e) {
                error_log("Buy cart error: " . $e->getMessage());
                echo json_encode(['status' => 'error', 'message' => 'Failed to process purchase']);
            }
            break;

        default:
            echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
            break;
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'No action specified']);
}
?>
