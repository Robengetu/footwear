<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
require_once 'database/connection.php';

function getCartItems($user_id) {
    return fetchAll(
        "SELECT c.id, c.quantity, p.name, p.price, p.original_price, p.is_sale 
         FROM cart c 
         JOIN products p ON c.product_id = p.id 
         WHERE c.user_id = ?", 
        [$user_id]
    );
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Checkout | Bits Footwear</title>
  <link rel="stylesheet" href="styles.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
  <div class="checkout-container">
    <div class="checkout-header">
      <h2><i class="fas fa-credit-card"></i> Confirm Your Order</h2>
      <a href="index.php" class="back-link"><i class="fas fa-arrow-left"></i> Back to Shop</a>
    </div>

    <div id="checkout-summary" class="checkout-summary">
      <?php 
        $cartItems = getCartItems($_SESSION['user_id']);
        if (!empty($cartItems)):
      ?>
        <h3>Cart Items</h3>
        <?php
          $total = 0;
          foreach ($cartItems as $item):
            $subtotal = $item['price'] * $item['quantity'];
            $total += $subtotal;
        ?>
          <div class="checkout-item">
            <div class="item-details">
              <h4><?= htmlspecialchars($item['name']) ?></h4>
              <p>Price: $<?= number_format($item['price'], 2) ?></p>
              <p>Quantity: <?= $item['quantity'] ?></p>
            </div>
            <div class="item-total">
              $<?= number_format($subtotal, 2) ?>
            </div>
          </div>
        <?php endforeach; ?>
        <div class="checkout-total">
          <h3>Total: $<?= number_format($total, 2) ?></h3>
        </div>
      <?php else: ?>
        <div class="empty-checkout">
          <i class="fas fa-shopping-cart"></i>
          <p>Your cart is empty.</p>
          <a href="index.php" class="btn">Continue Shopping</a>
        </div>
      <?php endif; ?>
    </div>

    <div class="payment-section">
      <h3><i class="fas fa-university"></i> Payment Information</h3>
      <div class="bank-info">
        <div class="bank-detail">
          <strong>Bank Name:</strong> XYZ Bank
        </div>
        <div class="bank-detail">
          <strong>Account Number:</strong> 1234567890
        </div>
        <div class="bank-detail">
          <strong>Account Name:</strong> Bits Footwear
        </div>
        <div class="bank-detail">
          <strong>Reference:</strong> Order #<?= date('YmdHis') ?>
        </div>
      </div>
    </div>

    <form action="backend/confirm_payment.php" method="POST" enctype="multipart/form-data" class="payment-form">
      <div class="form-group">
        <label for="screenshot">
          <i class="fas fa-camera"></i> Upload Payment Screenshot
        </label>
        <input type="file" name="screenshot" id="screenshot" accept="image/*" required>
        <small>Please upload a screenshot of your payment confirmation</small>
      </div>
      
      <div class="form-group">
        <label for="phone">
          <i class="fas fa-phone"></i> Phone Number
        </label>
        <input type="tel" name="phone" id="phone" required placeholder="Your phone number for order updates">
      </div>
      
      <button type="submit" class="confirm-btn">
        <i class="fas fa-check"></i> Confirm Payment
      </button>
    </form>
  </div>
</body>
</html>
