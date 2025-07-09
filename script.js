// Global variables
let cartData = { cart: [], totalQty: 0 };
let searchTimeout;

// DOM Content Loaded
document.addEventListener('DOMContentLoaded', function() {
    initializeApp();
});

// Initialize the application
function initializeApp() {
    setupEventListeners();
    loadCartData();
    setupSearchFunctionality();
}

// Set up all event listeners
function setupEventListeners() {
    // Tab navigation
    setupTabNavigation();
    
    // Cart functionality
    setupCartFunctionality();
    
    // Buy buttons
    setupBuyButtons();
    
    // Account dropdown
    setupAccountDropdown();
}

// Tab Navigation System
function setupTabNavigation() {
    const tabButtons = document.querySelectorAll('.tab-btn');
    const tabSections = document.querySelectorAll('.tab-section');
    
    tabButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            const targetId = this.getAttribute('data-target');
            
            // Remove active class from all buttons and sections
            tabButtons.forEach(btn => btn.classList.remove('active'));
            tabSections.forEach(section => section.classList.remove('active'));
            
            // Add active class to clicked button
            this.classList.add('active');
            
            // Show target section
            const targetSection = document.getElementById(targetId);
            if (targetSection) {
                targetSection.classList.add('active');
            }
            
            // Scroll to top of main content
            document.querySelector('.main-content').scrollIntoView({ 
                behavior: 'smooth',
                block: 'start'
            });
        });
    });
}

// Cart Functionality
function setupCartFunctionality() {
    // Clear cart button
    const clearCartBtn = document.getElementById('clear-cart');
    if (clearCartBtn) {
        clearCartBtn.addEventListener('click', clearCart);
    }
    
    // Buy all button
    const buyAllBtn = document.getElementById('buy-all');
    if (buyAllBtn) {
        buyAllBtn.addEventListener('click', buyAllItems);
    }
}

// Buy Buttons Functionality
function setupBuyButtons() {
    document.querySelectorAll('.buy-btn').forEach(button => {
        button.addEventListener('click', function() {
            const name = this.getAttribute('data-name');
            const price = parseFloat(this.getAttribute('data-price'));
            
            if (name && price) {
                buyNow(name, price);
            }
        });
    });
}

// Account Dropdown
function setupAccountDropdown() {
    const accountIcon = document.querySelector('.account-icon');
    const accountDropdown = document.getElementById('accountName');
    
    if (accountIcon && accountDropdown) {
        accountIcon.addEventListener('click', function(e) {
            e.stopPropagation();
            accountDropdown.classList.toggle('show');
        });
        
        // Close dropdown when clicking outside
        document.addEventListener('click', function() {
            accountDropdown.classList.remove('show');
        });
    }
}

// Toggle account dropdown (for backward compatibility)
function toggleAccountName() {
    const accountDropdown = document.getElementById('accountName');
    if (accountDropdown) {
        accountDropdown.classList.toggle('show');
    }
}

// Add item to cart
function addToCart(name, price) {
    showLoading();
    
    fetch('backend/cart_handler.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `action=add&name=${encodeURIComponent(name)}&price=${price}`
    })
    .then(response => response.json())
    .then(data => {
        hideLoading();
        
        if (data.status === 'added') {
            cartData = data;
            updateCartUI();
            showToast(`${name} added to cart!`, 'success');
        } else {
            showToast('Failed to add item to cart', 'error');
        }
    })
    .catch(error => {
        hideLoading();
        console.error('Error:', error);
        showToast('An error occurred while adding to cart', 'error');
    });
}

// Load cart data
function loadCartData() {
    fetch('backend/cart_handler.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'action=get_cart'
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            cartData = data;
            updateCartUI();
        }
    })
    .catch(error => {
        console.error('Error loading cart:', error);
    });
}

// Update cart UI
function updateCartUI() {
    const cartIcon = document.getElementById('cart-icon');
    const cartCount = document.querySelector('.cart-count');
    const cartItemsContainer = document.querySelector('.cart-items');
    const cartTotal = document.getElementById('cart-total');
    
    // Update cart count
    if (cartCount) {
        cartCount.textContent = cartData.totalQty || 0;
        cartCount.style.display = cartData.totalQty > 0 ? 'flex' : 'none';
    }
    
    // Update cart items display
    if (cartItemsContainer) {
        cartItemsContainer.innerHTML = '';
        
        if (cartData.cart.length === 0) {
            cartItemsContainer.innerHTML = '<p class="empty-cart">Your cart is empty.</p>';
            if (cartTotal) cartTotal.textContent = '';
            return;
        }
        
        let totalPrice = 0;
        cartData.cart.forEach(item => {
            const itemTotal = item.price * item.quantity;
            totalPrice += itemTotal;
            
            const cartItem = document.createElement('div');
            cartItem.className = 'cart-item';
            cartItem.innerHTML = `
                <div class="cart-item-details">
                    <strong>${item.name}</strong>
                    <div style="font-size: 0.85em; color: #888;">Qty: ${item.quantity}</div>
                </div>
                <div class="cart-item-price">
                    $${itemTotal.toFixed(2)}
                </div>
            `;
            cartItemsContainer.appendChild(cartItem);
        });
        
        if (cartTotal) {
            cartTotal.textContent = `Total: $${totalPrice.toFixed(2)}`;
        }
    }
}

// Add delegated event listener for quantity buttons
if (!window.qtyBtnListenerAdded) {
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('qty-btn')) {
            const name = e.target.getAttribute('data-name');
            const isIncrease = e.target.classList.contains('increase');
            const cartItem = e.target.closest('.cart-item');
            let qty = parseInt(cartItem.querySelector('.cart-item-qty').textContent);
            qty = isIncrease ? qty + 1 : Math.max(1, qty - 1);
            fetch('backend/cart_handler.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `action=update_quantity&name=${encodeURIComponent(name)}&quantity=${qty}`
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'updated') {
                    cartData = data;
                    updateCartUI();
                } else {
                    showToast('Failed to update quantity', 'error');
                }
            });
        }
    });
    window.qtyBtnListenerAdded = true;
}

// Clear cart
function clearCart() {
    if (cartData.cart.length === 0) {
        showToast('Cart is already empty', 'error');
        return;
    }
    
    if (confirm('Are you sure you want to clear your cart?')) {
        showLoading();
        
        fetch('backend/cart_handler.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'action=clear'
        })
        .then(response => response.json())
        .then(data => {
            hideLoading();
            
            if (data.status === 'cleared') {
                cartData = { cart: [], totalQty: 0 };
                updateCartUI();
                showToast('Cart cleared successfully!', 'success');
            } else {
                showToast('Failed to clear cart', 'error');
            }
        })
        .catch(error => {
            hideLoading();
            console.error('Error:', error);
            showToast('An error occurred while clearing cart', 'error');
        });
    }
}

// Buy all items in cart
function buyAllItems() {
    if (cartData.cart.length === 0) {
        showToast('Your cart is empty', 'error');
        return;
    }
    
    showLoading();
    
    fetch('backend/cart_handler.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'action=buy'
    })
    .then(response => response.json())
    .then(data => {
        hideLoading();
        
        if (data.status === 'success') {
            window.location.href = 'checkout.php';
        } else if (data.status === 'empty') {
            showToast('Your cart is empty', 'error');
        } else {
            showToast('Failed to proceed to checkout', 'error');
        }
    })
    .catch(error => {
        hideLoading();
        console.error('Error:', error);
        showToast('An error occurred while processing checkout', 'error');
    });
}

// Buy now (single item)
function buyNow(name, price) {
    showLoading();
    fetch('backend/cart_handler.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'action=clear'
    })
    .then(response => response.json())
    .then(data => {
        // Only proceed if clear was successful
        if (data.status === 'cleared') {
            fetch('backend/cart_handler.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=add&name=${encodeURIComponent(name)}&price=${price}&buy_now=1`
            })
            .then(response => response.json())
            .then(data => {
                hideLoading();
                if (data.status === 'added') {
                    setTimeout(() => {
                        window.location.href = 'checkout.php';
                    }, 200); // 200ms delay to show the active color
                } else {
                    showToast('Failed to add item to cart for checkout', 'error');
                }
            })
            .catch(error => {
                hideLoading();
                showToast('An error occurred while processing Buy Now', 'error');
            });
        } else {
            hideLoading();
            showToast('Failed to clear cart before Buy Now', 'error');
        }
    })
    .catch(error => {
        hideLoading();
        showToast('An error occurred while clearing cart', 'error');
    });
}

// Search Functionality
function setupSearchFunctionality() {
    const searchInput = document.querySelector('.search-input');
    const searchDropdown = document.querySelector('.search-dropdown');
    
    if (!searchInput || !searchDropdown) return;
    
    searchInput.addEventListener('input', function() {
        const query = this.value.trim();
        
        clearTimeout(searchTimeout);
        
        if (query.length < 2) {
            searchDropdown.classList.remove('show');
            return;
        }
        
        searchTimeout = setTimeout(() => {
            performSearch(query);
        }, 300);
    });
    
    // Close search dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (!searchInput.contains(e.target) && !searchDropdown.contains(e.target)) {
            searchDropdown.classList.remove('show');
        }
    });
}

// Perform search
function performSearch(query) {
    const searchDropdown = document.querySelector('.search-dropdown');
    
    fetch('backend/search.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `query=${encodeURIComponent(query)}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            displaySearchResults(data.results);
        } else {
            searchDropdown.innerHTML = '<li>No results found</li>';
            searchDropdown.classList.add('show');
        }
    })
    .catch(error => {
        console.error('Search error:', error);
        searchDropdown.innerHTML = '<li>Search error occurred</li>';
        searchDropdown.classList.add('show');
    });
}

// Display search results
function displaySearchResults(results) {
    const searchDropdown = document.querySelector('.search-dropdown');
    
    if (results.length === 0) {
        searchDropdown.innerHTML = '<li>No products found</li>';
    } else {
        searchDropdown.innerHTML = results.map(product => 
            `<li onclick="selectSearchResult('${product.name}', ${product.price})">
                <strong>${product.name}</strong> - $${product.price}
            </li>`
        ).join('');
    }
    
    searchDropdown.classList.add('show');
}

// Select search result
function selectSearchResult(name, price) {
    const searchInput = document.querySelector('.search-input');
    const searchDropdown = document.querySelector('.search-dropdown');
    
    searchInput.value = name;
    searchDropdown.classList.remove('show');
    
    // Optional: Scroll to the product or add to cart
    showToast(`Found: ${name} - $${price}`, 'success');
}

// Scroll to products section
function scrollToProducts() {
    const productsSection = document.getElementById('stylish-products');
    if (productsSection) {
        productsSection.scrollIntoView({ 
            behavior: 'smooth',
            block: 'start'
        });
    }
}

// Utility Functions

// Show loading spinner
function showLoading() {
    const spinner = document.getElementById('loading-spinner');
    if (spinner) {
        spinner.style.display = 'flex';
    }
}

// Hide loading spinner
function hideLoading() {
    const spinner = document.getElementById('loading-spinner');
    if (spinner) {
        spinner.style.display = 'none';
    }
}

// Show toast notification
function showToast(message, type = 'success') {
    const toast = document.getElementById('toast');
    if (!toast) return;
    
    toast.textContent = message;
    toast.className = `toast ${type}`;
    toast.classList.add('show');
    
    setTimeout(() => {
        toast.classList.remove('show');
    }, 3000);
}

// Format currency
function formatCurrency(amount) {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD'
    }).format(amount);
}

// Debounce function
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Initialize on page load
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializeApp);
} else {
    initializeApp();
}

// Replace the previous event delegation with this improved version:
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('buy-btn')) {
        e.stopPropagation(); // Prevent bubbling to add-to-cart
        const name = e.target.getAttribute('data-name');
        const price = parseFloat(e.target.getAttribute('data-price'));
        if (name && price) {
            buyNow(name, price);
        }
    } else if (e.target.classList.contains('add-to-cart')) {
        const name = e.target.getAttribute('data-name');
        const price = parseFloat(e.target.getAttribute('data-price'));
        if (name && price) {
            addToCart(name, price);
        }
    }
});
