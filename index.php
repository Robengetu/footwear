<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Bits Footwear</title>
  <link rel="stylesheet" href="styles.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
  <header class="header">
    <div class="container">
      <div class="logo">
        <p class="welcome-text">Welcome, <?= htmlspecialchars($_SESSION['username']); ?> | <a href="backend/logout.php">Logout</a></p>
        <span class="bits-logo"><i class="fas fa-shoe-prints"></i> BITS FOOTWEAR</span>
      </div>
      <nav class="nav">
        <a href="#" class="nav-link tab-btn active" data-target="home">Home</a>
        <a href="#" class="nav-link tab-btn" data-target="men">Men</a>
        <a href="#" class="nav-link tab-btn" data-target="women">Women</a>
        <a href="#" class="nav-link tab-btn" data-target="kids">Kids</a>
        <a href="#" class="nav-link tab-btn" data-target="sale">Sale</a>
      </nav>
      <div class="header-actions">
        <div class="search-box">
          <input type="text" placeholder="Search products..." class="search-input" autocomplete="off">
          <ul class="search-dropdown"></ul>
        </div>        
        <div class="user-icons">
          <span class="icon wishlist-icon" title="Wishlist"><i class="fas fa-heart"></i></span>
          <span class="icon account-icon" onclick="toggleAccountName()" title="Account"><i class="fas fa-user"></i></span>
          <div id="accountName" class="account-dropdown">
            <p><?= htmlspecialchars($_SESSION['username']); ?></p>
          </div>
          <span class="icon cart-icon" id="cart-icon" data-count="0" title="View Cart" onclick="document.querySelector('.shopping-cart').scrollIntoView({ behavior: 'smooth' });">
            <i class="fas fa-shopping-cart"></i>
            <span class="cart-count">0</span>
          </span>
          <?php if (isset($_SESSION['user_id'])): ?>
            <a href="backend/logout.php" class="icon logout-icon" title="Logout" style="margin-left: 8px;"><i class="fas fa-sign-out-alt"></i></a>
          <?php endif; ?>
        </div>        
      </div>
    </div>
  </header>
  <main class="main-content">
    <div class="container">
      <section id="home" class="tab-section active">
        <div class="container hero-container">
          <div class="hero-image-landing">
            <img src="images/v2_84.png" alt="Landing Product" class="hero-bg-image">
            <div class="hero-overlay-content">
              <h1>Step into Style</h1>
              <p>Explore our latest collection of footwear, designed for comfort and performance.</p>
              <button class="cta-button" onclick="document.getElementById('stylish-products').scrollIntoView({ behavior: 'smooth' });">Shop Now</button>
            </div>
          </div>
        </div>
        <section class="our-story">
          <h2>Our Story</h2>
          <p>At Bits Footwear, we believe that the right pair of shoes can transform your day. Our journey began with a passion for quality craftsmanship and a commitment to creating footwear that combines style, comfort, and durability. From classic designs to cutting-edge innovations, we offer a diverse range of shoes to suit every lifestyle and preference. Discover the perfect fit and step into a world of style with us.</p>
        </section>
        <section class="contact-us">
          <h2>Contact Us</h2>
          <p>Got a question or browse suggestions? Reach out to us at <strong>info@bitsfootwear.com</strong> or call us at <strong>(+251) 912345678</strong>. We're here to help you find the perfect pair of shoes.</p>
        </section>
        <section id="stylish-products" class="stylish-products">
          <h2>Featured Products</h2>
          <div class="products-grid">
            <div class="product-card">
              <div class="product-image-container">
              <img src="images/v2_106.png" alt="PRODUCT NAME" class="product-image">
              </div>
              <h3>Air Jordan 1 Low Dunk Green</h3>
              <p class="product-description">Experience ultimate comfort</p>
              <p class="price">$150</p>
              <div class="product-actions">
                <button class="add-to-cart" data-name="Air Jordan 1 Low Dunk Green" data-price="150">Add to Cart</button>
                <button class="buy-btn" data-name="Air Jordan 1 Low Dunk Green" data-price="150">Buy Now</button>
              </div>
            </div>
            <div class="product-card">
              <div class="product-image-container">
              <img src="images/v2_113.png" alt="PRODUCT NAME" class="product-image">
              </div>
              <h3>Nike Dunk Low Off-White Lot 1</h3>
              <p class="product-description">Premium quality footwear</p>
              <p class="price">$180</p>
              <div class="product-actions">
                <button class="add-to-cart" data-name="Nike Dunk Low Off-White Lot 1" data-price="180">Add to Cart</button>
                <button class="buy-btn" data-name="Nike Dunk Low Off-White Lot 1" data-price="180">Buy Now</button>
              </div>
            </div>
            <div class="product-card">
              <div class="product-image-container">
              <img src="images/v2_120.png" alt="PRODUCT NAME" class="product-image">
              </div>
              <h3>Air Jordan 1 High OG 'Bloodline'</h3>
              <p class="product-description">Classic design meets modern comfort</p>
              <p class="price">$200</p>
              <div class="product-actions">
                <button class="add-to-cart" data-name="Air Jordan 1 High OG 'Bloodline'" data-price="200">Add to Cart</button>
                <button class="buy-btn" data-name="Air Jordan 1 High OG 'Bloodline'" data-price="200">Buy Now</button>
              </div>
            </div>
            <div class="product-card">
              <div class="product-image-container">
              <img src="images/v2_127.png" alt="PRODUCT NAME" class="product-image">
              </div>
              <h3>Travis Scott x Air Jordan 1</h3>
              <p class="product-description">Crafted with premium materials</p>
              <p class="price">$220</p>
              <div class="product-actions">
                <button class="add-to-cart" data-name="Travis Scott x Air Jordan 1" data-price="220">Add to Cart</button>
                <button class="buy-btn" data-name="Travis Scott x Air Jordan 1" data-price="220">Buy Now</button>
              </div>
            </div>
          </div>
        </section>
        <section class="shoe-categories">
          <h2>Shoe Categories</h2>
          <div class="category-tabs">
            <button class="category-tab active">Men</button>
            <button class="category-tab">Women</button>
            <button class="category-tab">Kids</button>
            <button class="category-tab">Sale</button>
          </div>
          <div class="categories-grid">
            <div class="category-card">
              <div class="category-icon">
                <img src="images/v2_181.png" alt="Urban Glide" />
              </div>
              <h3>Urban Glide</h3>
              <p>Comfortable and stylish for city walks.</p>
            </div>
            <div class="category-card">
              <div class="category-icon">
                <img src="images/v2_188.png" alt="Skybound" />
              </div>
              <h3>Skybound</h3>
              <p>Lightweight design for high-impact activities.</p>
            </div>
            <div class="category-card">
              <div class="category-icon">
                <img src="images/v2_195.png" alt="Trailblazer X" />
              </div>
              <h3>Trailblazer X</h3>
              <p>Durable boots for outdoor adventures.</p>
            </div>
            <div class="category-card">
              <div class="category-icon">
                <img src="images/v2_202.png" alt="Velocity Pro" />
              </div>
              <h3>Velocity Pro</h3>
              <p>Responsive cushioning for optimal performance.</p>
            </div>
          </div>
        </section>
        <section class="shopping-cart">
          <h2>Shopping Cart Preview</h2>
          <div class="cart-items"></div>
          <div id="cart-total" class="cart-total"></div>
          <div class="cart-actions">
            <button id="clear-cart" class="clear-cart-btn">Clear Cart</button>
            <button id="buy-all" class="buy-all-btn">Buy All</button>
          </div>
        </section>
      </section>
      <section id="men" class="tab-section">
        <h2>Men's Collection</h2>
        <p>Discover our premium collection of men's footwear.</p>
        <div class="products-grid">
          <div class="product-card">
            <div class="product-image-container">
              <img src= "images/Nike Air Max 90 Men's Shoes.jpg" alt="Men's Air Max Runner" class="product-image">
            </div>
            <h3>Men's Air Max Runner</h3>
            <p class="product-description">Superior comfort for daily wear</p>
            <p class="price">$120</p>
            <div class="product-actions">
              <button class="add-to-cart" data-name="Men's Air Max Runner" data-price="120">Add to Cart</button>
              <button class="buy-btn" data-name="Men's Air Max Runner" data-price="120">Buy Now</button>
            </div>
          </div>
          <div class="product-card">
            <div class="product-image-container">
              <img src="images/download (1).jpg" alt="Men's Classic High Top" class="product-image">
            </div>
            <h3>Men's Classic High Top</h3>
            <p class="product-description">Timeless style and durability</p>
            <p class="price">$140</p>
            <div class="product-actions">
              <button class="add-to-cart" data-name="Men's Classic High Top" data-price="140">Add to Cart</button>
              <button class="buy-btn" data-name="Men's Classic High Top" data-price="140">Buy Now</button>
            </div>
          </div>
          <div class="product-card">
            <div class="product-image-container">
              <img src="images/v2_137.png" alt="Men's Sport Pro" class="product-image">
            </div>
            <h3>Men's Sport Pro</h3>
            <p class="product-description">Athletic performance redefined</p>
            <p class="price">$160</p>
            <div class="product-actions">
              <button class="add-to-cart" data-name="Men's Sport Pro" data-price="160">Add to Cart</button>
              <button class="buy-btn" data-name="Men's Sport Pro" data-price="160">Buy Now</button>
            </div>
          </div>
        </div>
      </section>
      <section id="women" class="tab-section">
        <h2>Women's Collection</h2>
        <p>Explore stylish and comfortable shoes for women.</p>
        <div class="products-grid">
          <div class="product-card">
            <div class="product-image-container">
              <img src="images/Slide View_ 1_ Birkenstock Navy Arizona Sandals.jpg" alt="Women's Stylish Slip-On" class="product-image">
            </div>
            <h3>Women's Stylish Slip-On</h3>
            <p class="product-description">Easy wear meets elegant style</p>
            <p class="price">$110</p>
            <div class="product-actions">
              <button class="add-to-cart" data-name="Women's Stylish Slip-On" data-price="110">Add to Cart</button>
              <button class="buy-btn" data-name="Women's Stylish Slip-On" data-price="110">Buy Now</button>
            </div>
          </div>
          <div class="product-card">
            <div class="product-image-container">
              <img src="images/Ron Holt (@CharlesHDavis2) on X.jpg" alt="Women's Air Boost" class="product-image">
            </div>
            <h3>Women's Air Boost</h3>
            <p class="product-description">Lightweight with superior cushioning</p>
            <p class="price">$120</p>
            <div class="product-actions">
              <button class="add-to-cart" data-name="Women's Air Boost" data-price="120">Add to Cart</button>
              <button class="buy-btn" data-name="Women's Air Boost" data-price="120">Buy Now</button>
            </div>
          </div>
          <div class="product-card">
            <div class="product-image-container">
              <img src="images/Women's Stiletto High Heel Pumps Point Toe Fashionable Elegant Solid Color Court Pumps Shoes.jpg" alt="Women's Elegance Heel" class="product-image">
            </div>
            <h3>Women's Elegance Heel</h3>
            <p class="product-description">Perfect for formal occasions</p>
            <p class="price">$135</p>
            <div class="product-actions">
              <button class="add-to-cart" data-name="Women's Elegance Heel" data-price="135">Add to Cart</button>
              <button class="buy-btn" data-name="Women's Elegance Heel" data-price="135">Buy Now</button>
            </div>
          </div>
        </div>
      </section>
      <section id="kids" class="tab-section">
        <h2>Kids' Collection</h2>
        <p>Fun and durable shoes made for kids on the move.</p>
        <div class="products-grid">
          <div class="product-card">
            <div class="product-image-container">
              <img src="images/Boys' Puma Softride One4All Fade Running Shoes in Black_RED_FADE Size 1_5 - Little Kid.jpg" alt="Kids Classic Runner" class="product-image">
            </div>
            <h3>Kids Classic Runner</h3>
            <p class="product-description">Perfect for active kids</p>
            <p class="price">$90</p>
            <div class="product-actions">
              <button class="add-to-cart" data-name="Kids Classic Runner" data-price="90">Add to Cart</button>
              <button class="buy-btn" data-name="Kids Classic Runner" data-price="90">Buy Now</button>
            </div>
          </div>
          <div class="product-card">
            <div class="product-image-container">
              <img src="images\Puma Kids Future 8 Match Creativity FG AG.jpg" alt="Mini Sports Pro" class="product-image">
            </div>
            <h3>Mini Sports Pro</h3>
            <p class="product-description">Athletic comfort for young athletes</p>
            <p class="price">$95</p>
            <div class="product-actions">
              <button class="add-to-cart" data-name="Mini Sports Pro" data-price="95">Add to Cart</button>
              <button class="buy-btn" data-name="Mini Sports Pro" data-price="95">Buy Now</button>
            </div>
          </div>
          <div class="product-card">
            <div class="product-image-container">
              <img src="images\adidas Samba OG Shoes Kids - White _ Free Shipping with adiClub _ adidas US.jpg" alt="Kids Adventure Boot" class="product-image">
            </div>
            <h3>Kids Adventure Boot</h3>
            <p class="product-description">Durable protection for outdoor play</p>
            <p class="price">$105</p>
            <div class="product-actions">
              <button class="add-to-cart" data-name="Kids Adventure Boot" data-price="105">Add to Cart</button>
              <button class="buy-btn" data-name="Kids Adventure Boot" data-price="105">Buy Now</button>
            </div>
          </div>
        </div>
      </section>
      <section id="sale" class="tab-section">
        <h2>On Sale</h2>
        <p>Don't miss out on our latest discounts and deals!</p>
        <div class="products-grid">
          <div class="product-card sale-item">
            <div class="product-image-container">
              <img src="images\Yeezy Shoes _ Adidas Yeezy Foam Rnnr - Stone Salt _ Color_ Tan _ Size_ 10.jpg" alt="Discounted Runner" class="product-image">
              <span class="sale-badge">Sale</span>
            </div>
            <h3>Discounted Runner</h3>
            <p class="product-description">Great quality at a great price</p>
            <p class="price">
              <span class="original-price">$150</span>
              <span class="sale-price">$120</span>
            </p>
            <div class="product-actions">
              <button class="add-to-cart" data-name="Discounted Runner" data-price="120">Add to Cart</button>
              <button class="buy-btn" data-name="Discounted Runner" data-price="120">Buy Now</button>
            </div>
          </div>
          <div class="product-card sale-item">
            <div class="product-image-container">
              <img src="images\Women Hands Free Slip-Ins Summits Brilliant Shine Slip-On Sneaker -.jpg" alt="Clearance Slip-On" class="product-image">
              <span class="sale-badge">Sale</span>
            </div>
            <h3>Clearance Slip-On</h3>
            <p class="product-description">Limited time offer</p>
            <p class="price">
              <span class="original-price">$100</span>
              <span class="sale-price">$75</span>
            </p>
            <div class="product-actions">
              <button class="add-to-cart" data-name="Clearance Slip-On" data-price="75">Add to Cart</button>
              <button class="buy-btn" data-name="Clearance Slip-On" data-price="75">Buy Now</button>
            </div>
          </div>
          <div class="product-card sale-item">
            <div class="product-image-container">
              <img src="images\v2_202.png" alt="End of Season Boot" class="product-image">
              <span class="sale-badge">Sale</span>
            </div>
            <h3>End of Season Boot</h3>
            <p class="product-description">Last chance to grab these</p>
            <p class="price">
              <span class="original-price">$180</span>
              <span class="sale-price">$135</span>
            </p>
            <div class="product-actions">
              <button class="add-to-cart" data-name="End of Season Boot" data-price="135">Add to Cart</button>
              <button class="buy-btn" data-name="End of Season Boot" data-price="135">Buy Now</button>
            </div>
          </div>
        </div>
      </section>
    </div>
  </main>
  <div id="loading-spinner" class="loading-spinner">
    <div class="spinner"></div>
  </div>
  <div id="toast" class="toast"></div>
  <script src="script.js"></script>
</body>
</html>
