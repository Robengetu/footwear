<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

$success = false;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $phone = $_POST['phone'] ?? '';
    
    // Handle file upload
    if (isset($_FILES['screenshot']) && $_FILES['screenshot']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../uploads/';
        
        // Create uploads directory if it doesn't exist
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        $fileName = time() . '_' . $_SESSION['user_id'] . '_' . basename($_FILES['screenshot']['name']);
        $targetPath = $uploadDir . $fileName;
        
        // Validate file type
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $fileType = $_FILES['screenshot']['type'];
        
        if (!in_array($fileType, $allowedTypes)) {
            $error = 'Invalid file type. Please upload an image file.';
        } elseif ($_FILES['screenshot']['size'] > 5000000) { // 5MB limit
            $error = 'File size too large. Maximum 5MB allowed.';
        } elseif (move_uploaded_file($_FILES['screenshot']['tmp_name'], $targetPath)) {
            // File uploaded successfully
            $success = true;
            
            // Clear cart after successful payment
            $_SESSION['cart'] = [];
            
            // Clear checkout item from localStorage (handled by JavaScript)
            
            // In a real application, you would:
            // 1. Save order details to database
            // 2. Send confirmation email
            // 3. Update inventory
            // 4. Process payment verification
            if ($success) {
                require_once '../database/connection.php';
            
                $pdo = new PDO("mysql:host=localhost;port=3307;dbname=footwear", "root", "");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                $user_id = $_SESSION['user_id'];
            
                // Fetch cart items
                $cartItems = fetchAll(
                    "SELECT c.product_id, c.quantity, p.price 
                     FROM cart c 
                     JOIN products p ON c.product_id = p.id 
                     WHERE c.user_id = ?",
                    [$user_id]
                );
            
                if (empty($cartItems)) {
                    $error = 'Your cart is empty. Please add items before confirming payment.';
                    $success = false;
                } else {
                    // Calculate total
                    $totalAmount = 0;
                    foreach ($cartItems as $item) {
                        $totalAmount += $item['price'] * $item['quantity'];
                    }
            
                    try {
                        $pdo->beginTransaction();
            
                        // Insert into orders table
                        $stmt = $pdo->prepare("INSERT INTO orders (user_id, phone, total_amount, payment_screenshot) VALUES (?, ?, ?, ?)");
                        $stmt->execute([$user_id, $phone, $totalAmount, $fileName]);
            
                        $orderId = $pdo->lastInsertId();
            
                        // Insert into order_items table
                        $stmtItem = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
                        foreach ($cartItems as $item) {
                            $stmtItem->execute([$orderId, $item['product_id'], $item['quantity'], $item['price']]);
                        }
            
                        // Clear cart table for the user
                        $pdo->prepare("DELETE FROM cart WHERE user_id = ?")->execute([$user_id]);
            
                        $pdo->commit();
            
                        $success = true;
            
                    } catch (Exception $e) {
                        $pdo->rollBack();
                        $error = 'Order processing failed: ' . $e->getMessage();
                        $success = false;
                    }
                }
            }
            
        } else {
            $error = 'Failed to upload payment screenshot.';
        }
    } else {
        $error = 'Please upload a payment screenshot.';
    }
    
    if (empty($phone)) {
        $error = 'Please provide your phone number.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Confirmation - Bits Footwear</title>
    <link rel="stylesheet" href="../styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
    <div class="checkout-container">
        <?php if ($success): ?>
            <div class="success-container">
                <div class="success-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <h2>Payment Confirmation Received!</h2>
                <p>Thank you for your order. We have received your payment screenshot and will process your order shortly.</p>
                <p>You will receive a confirmation call on your provided phone number within 24 hours.</p>
                
                <div class="order-summary">
                    <h3>What's Next?</h3>
                    <ul>
                        <li><i class="fas fa-check"></i> Payment verification (1-2 hours)</li>
                        <li><i class="fas fa-check"></i> Order confirmation call</li>
                        <li><i class="fas fa-check"></i> Order processing (1-2 days)</li>
                        <li><i class="fas fa-check"></i> Shipping & delivery (3-5 days)</li>
                    </ul>
                </div>
                
                <div class="action-buttons">
                    <a href="../index.php" class="btn btn-primary">
                        <i class="fas fa-home"></i> Continue Shopping
                    </a>
                </div>
            </div>
        <?php else: ?>
            <div class="error-container">
                <div class="error-icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <h2>Payment Confirmation Failed</h2>
                <?php if ($error): ?>
                    <p class="error-message"><?= htmlspecialchars($error) ?></p>
                <?php endif; ?>
                
                <div class="action-buttons">
                    <a href="../checkout.php" class="btn btn-primary">
                        <i class="fas fa-arrow-left"></i> Try Again
                    </a>
                    <a href="../index.php" class="btn btn-secondary">
                        <i class="fas fa-home"></i> Back to Shop
                    </a>
                </div>
            </div>
        <?php endif; ?>
    </div>
    
    <script>
        <?php if ($success): ?>
            // Clear checkout item from localStorage
            localStorage.removeItem('checkout_item');
        <?php endif; ?>
    </script>
    
    <style>
        .success-container,
        .error-container {
            text-align: center;
            padding: 2rem;
        }
        
        .success-icon i {
            font-size: 4rem;
            color: #27ae60;
            margin-bottom: 1rem;
        }
        
        .error-icon i {
            font-size: 4rem;
            color: #e74c3c;
            margin-bottom: 1rem;
        }
        
        .success-container h2 {
            color: #27ae60;
            margin-bottom: 1rem;
        }
        
        .error-container h2 {
            color: #e74c3c;
            margin-bottom: 1rem;
        }
        
        .order-summary {
            background: #f8f9fa;
            padding: 1.5rem;
            border-radius: 8px;
            margin: 2rem 0;
            text-align: left;
        }
        
        .order-summary h3 {
            color: #2c3e50;
            margin-bottom: 1rem;
        }
        
        .order-summary ul {
            list-style: none;
            padding: 0;
        }
        
        .order-summary li {
            padding: 0.5rem 0;
            color: #555;
        }
        
        .order-summary li i {
            color: #27ae60;
            margin-right: 0.5rem;
        }
        
        .action-buttons {
            margin-top: 2rem;
            display: flex;
            gap: 1rem;
            justify-content: center;
        }
        
        .btn {
            padding: 0.75rem 1.5rem;
            text-decoration: none;
            border-radius: 8px;
            font-weight: bold;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .btn-primary {
            background: #3498db;
            color: white;
        }
        
        .btn-primary:hover {
            background: #2980b9;
        }
        
        .btn-secondary {
            background: #95a5a6;
            color: white;
        }
        
        .btn-secondary:hover {
            background: #7f8c8d;
        }
        
        .error-message {
            color: #e74c3c;
            font-weight: bold;
            margin-bottom: 1rem;
        }
        
        @media (max-width: 600px) {
            .action-buttons {
                flex-direction: column;
            }
        }
    </style>
</body>
</html>
