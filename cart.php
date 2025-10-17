<?php
session_start();

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$database = "booksy";
$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle cart actions
if (isset($_GET['action'])) {
    $isbn = isset($_GET['isbn']) ? $_GET['isbn'] : '';
    $qty = isset($_GET['qty']) ? (int)$_GET['qty'] : 1;

    switch ($_GET['action']) {
        case 'add':
            // Add to cart or update quantity
            if (!empty($isbn)) {
                if (isset($_SESSION['cart'][$isbn])) {
                    $_SESSION['cart'][$isbn] += $qty;
                } else {
                    $_SESSION['cart'][$isbn] = $qty;
                }
                // Optional: Add a limit for maximum quantity (e.g., 10)
                if ($_SESSION['cart'][$isbn] > 10) {
                    $_SESSION['cart'][$isbn] = 10;
                }
            }
            // If coming from shop detail page, redirect back to cart
            if (isset($_GET['from']) && $_GET['from'] == 'detail') {
                header("Location: cart.php");
                exit();
            }
            break;

        case 'update':
            // Update quantity
            if (!empty($isbn) && $qty > 0) {
                $_SESSION['cart'][$isbn] = $qty;
                // Optional: Add a limit for maximum quantity
                if ($_SESSION['cart'][$isbn] > 10) {
                    $_SESSION['cart'][$isbn] = 10;
                }
            }
            // If this is an AJAX request, send a success response
            if (isset($_GET['ajax']) && $_GET['ajax'] == 1) {
                // Recalculate totals
                recalculateCart($conn);
                echo json_encode([
                    'success' => true,
                    'qty' => $_SESSION['cart'][$isbn],
                    'totals' => [
                        'subtotal' => number_format($_SESSION['cart_subtotal'], 2),
                        'discount' => isset($_SESSION['discount']) ? number_format($_SESSION['discount'], 2) : 0,
                        'final' => number_format($_SESSION['cart_final_total'], 2)
                    ]
                ]);
                exit();
            }
            break;

        case 'remove':
            // Remove from cart
            if (isset($_SESSION['cart'][$isbn])) {
                unset($_SESSION['cart'][$isbn]);
                
                // Check if cart is empty after removing item
                if (empty($_SESSION['cart'])) {
                    // If cart is now empty, remove any discount
                    if (isset($_SESSION['discount'])) {
                        unset($_SESSION['discount']);
                    }
                    if (isset($_SESSION['coupon_code'])) {
                        unset($_SESSION['coupon_code']);
                    }
                    // Reset cart totals to zero
                    $_SESSION['cart_subtotal'] = 0;
                    $_SESSION['cart_final_total'] = 0;
                } else {
                    // Cart still has items, recalculate
                    recalculateCart($conn);
                }
            }
            break;

        case 'clear':
            // Clear entire cart
            $_SESSION['cart'] = [];
            // Remove any discount
            if (isset($_SESSION['discount'])) {
                unset($_SESSION['discount']);
            }
            if (isset($_SESSION['coupon_code'])) {
                unset($_SESSION['coupon_code']);
            }
            // Reset cart totals
            $_SESSION['cart_subtotal'] = 0;
            $_SESSION['cart_final_total'] = 0;
            break;
    }

    // Redirect back to cart page if this was a POST action
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        header("Location: cart.php");
        exit();
    }
}

// Function to recalculate cart totals
function recalculateCart($conn)
{
    $totalPrice = 0;

    if (!empty($_SESSION['cart'])) {
        // Create placeholders for the IN clause
        $placeholders = str_repeat('?,', count(array_keys($_SESSION['cart'])) - 1) . '?';

        // Prepare SQL statement
        $sql = "SELECT isbn, price FROM books WHERE isbn IN ($placeholders)";
        $stmt = $conn->prepare($sql);

        // Bind parameters dynamically
        $types = str_repeat('s', count($_SESSION['cart']));
        $stmt->bind_param($types, ...array_keys($_SESSION['cart']));

        $stmt->execute();
        $result = $stmt->get_result();

        while ($book = $result->fetch_assoc()) {
            $qty = $_SESSION['cart'][$book['isbn']];
            $subtotal = $book['price'] * $qty;
            $totalPrice += $subtotal;
        }

        $stmt->close();
    }

    $_SESSION['cart_subtotal'] = $totalPrice;

    // Apply discount if applicable and cart is not empty
    if ($totalPrice > 0 && isset($_SESSION['discount'])) {
        $discount = $_SESSION['discount'];
        // Make sure discount doesn't exceed the total price
        if ($discount > $totalPrice) {
            $discount = $totalPrice;
            $_SESSION['discount'] = $discount;
        }
    } else {
        $discount = 0;
        // If cart total is zero or very small, remove any discount
        if (isset($_SESSION['discount'])) {
            unset($_SESSION['discount']);
        }
        if (isset($_SESSION['coupon_code'])) {
            unset($_SESSION['coupon_code']);
        }
    }

    $_SESSION['cart_final_total'] = $totalPrice - $discount;

    return $_SESSION['cart_final_total'];
}

// Get cart items details from database
$cartItems = [];
$totalPrice = 0;

if (!empty($_SESSION['cart'])) {
    // Create placeholders for the IN clause
    $placeholders = str_repeat('?,', count(array_keys($_SESSION['cart'])) - 1) . '?';

    // Prepare SQL statement
    $sql = "SELECT isbn, title, author, price, coverimg FROM books WHERE isbn IN ($placeholders)";
    $stmt = $conn->prepare($sql);

    // Bind parameters dynamically
    $types = str_repeat('s', count($_SESSION['cart']));
    $stmt->bind_param($types, ...array_keys($_SESSION['cart']));

    $stmt->execute();
    $result = $stmt->get_result();

    while ($book = $result->fetch_assoc()) {
        $qty = $_SESSION['cart'][$book['isbn']];
        $subtotal = $book['price'] * $qty;
        $totalPrice += $subtotal;

        $cartItems[] = [
            'isbn' => $book['isbn'],
            'title' => $book['title'],
            'author' => $book['author'],
            'price' => $book['price'],
            'coverimg' => $book['coverimg'],
            'qty' => $qty,
            'subtotal' => $subtotal
        ];
    }

    $stmt->close();
}

// Save cart subtotal for later use
$_SESSION['cart_subtotal'] = $totalPrice;

// Handle coupon application
$discount = 0;
if (isset($_POST['apply_coupon']) && !empty($_POST['coupon_code'])) {
    // Only apply coupon if cart is not empty
    if (!empty($cartItems)) {
        // You can implement coupon logic here
        // For now, let's just have a sample coupon "BOOKSY10" for 10% off
        if ($_POST['coupon_code'] === 'BOOKSY10') {
            $discount = $totalPrice * 0.5;
            $_SESSION['discount'] = $discount;
            $_SESSION['coupon_code'] = $_POST['coupon_code'];
        }
    }
} else if (isset($_SESSION['discount']) && !empty($cartItems)) {
    $discount = $_SESSION['discount'];
    // Make sure discount doesn't exceed the total price
    if ($discount > $totalPrice) {
        $discount = $totalPrice;
        $_SESSION['discount'] = $discount;
    }
} else {
    // If cart is empty, remove any discount
    if (isset($_SESSION['discount'])) {
        unset($_SESSION['discount']);
    }
    if (isset($_SESSION['coupon_code'])) {
        unset($_SESSION['coupon_code']);
    }
}

$finalTotal = $totalPrice - $discount;
$_SESSION['cart_final_total'] = $finalTotal;

// Calculate cart item count for the header badge
$cartItemCount = 0;
foreach ($_SESSION['cart'] as $qty) {
    $cartItemCount += $qty;
}

include 'Pages/header.php';
?>

<link rel="stylesheet" href="cart.css">

<style>
    .empty-cart-container {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        padding: 60px 20px;
        margin: 30px 0;
        background-color: #f8f9fa;
        border-radius: 12px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
    }

    .empty-cart-icon {
        font-size: 80px;
        color: #d0d0d0;
        margin-bottom: 25px;
        animation: float 3s ease-in-out infinite;
    }

    @keyframes float {
        0% {
            transform: translateY(0px);
        }

        50% {
            transform: translateY(-15px);
        }

        100% {
            transform: translateY(0px);
        }
    }

    .empty-cart-title {
        font-size: 28px;
        font-weight: 600;
        margin-bottom: 15px;
        color: #333;
    }

    .empty-cart-message {
        font-size: 16px;
        color: #666;
        margin-bottom: 30px;
        max-width: 500px;
    }



    /* Visual book elements */
    .floating-books {
        position: relative;
        width: 100%;
        height: 120px;
        margin-bottom: 20px;
    }

    .floating-book {
        position: absolute;
        transform: rotate(var(--rotation));
        opacity: 0.8;
        animation: floatBook 4s ease-in-out infinite;
        animation-delay: var(--delay);
    }

    .floating-book.book1 {
        left: calc(50% - 120px);
        --rotation: -15deg;
        --delay: 0s;
    }

    .floating-book.book2 {
        left: calc(50% - 30px);
        --rotation: 5deg;
        --delay: 0.5s;
    }

    .floating-book.book3 {
        left: calc(50% + 60px);
        --rotation: -10deg;
        --delay: 1s;
    }

    @keyframes floatBook {
        0% {
            transform: translateY(0) rotate(var(--rotation));
        }

        50% {
            transform: translateY(-20px) rotate(var(--rotation));
        }

        100% {
            transform: translateY(0) rotate(var(--rotation));
        }
    }
</style>

<div class="page-navigation-wrapper">
    <div class="book1">
        <img src="/Booksy/Assets/shop-default/book1.png" alt="book">
    </div>
    <div class="book2">
        <img src="/Booksy/Assets/shop-default/book2.png" alt="book">
    </div>
    <div class="container">
        <div class="page-heading">
            <h1>Cart</h1>
            <div class="page-header">
                <ul class="custom-navigation">
                    <li><a href="/Booksy/index.php">Home</a></li>
                    <li><i class="bi bi-chevron-right"></i></li>
                    <li><a href="/Booksy/shop.php">Shop</a></li>
                    <li><i class="bi bi-chevron-right"></i></li>
                    <li>Cart</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="row g-5 cart-container">
    <div class="col-xl-9">
        <div class="table-responsive">
            <?php if (empty($cartItems)): ?>
                <!-- Improved Empty Cart Display -->
                <div class="empty-cart-container">
                    <div class="floating-books">
                        <div class="floating-book book1">
                            <i class="bi bi-book" style="font-size: 45px; color: #ff6b6b;"></i>
                        </div>
                        <div class="floating-book book2">
                            <i class="bi bi-journal-bookmark" style="font-size: 50px; color: #5f76e8;"></i>
                        </div>
                        <div class="floating-book book3">
                            <i class="bi bi-book-half" style="font-size: 45px; color: #20c997;"></i>
                        </div>
                    </div>
                    <i class="bi bi-cart-x empty-cart-icon"></i>
                    <h2 class="empty-cart-title">Your Cart is Empty</h2>
                    <p class="empty-cart-message">
                        Looks like you haven't added any books to your cart yet.<br>
                        Explore our collection and find your next favorite read!
                    </p>
                </div>
            <?php else: ?>
                <!-- Cart Items Display -->
                <div class="cart-items-container">
                    <?php foreach ($cartItems as $item): ?>
                        <div class="cart-item">
                            <div class="item-container">
                                <!-- Book Image Container -->
                                <div class="product-image-container">
                                    <img src="<?php echo htmlspecialchars($item['coverimg']); ?>" alt="<?php echo htmlspecialchars($item['title']); ?>" class="book-cover-image">
                                </div>

                                <!-- Book Information -->
                                <div class="book-details">
                                    <h3 class="book-title"><?php echo htmlspecialchars($item['title']); ?></h3>
                                    <p class="book-author">by <?php echo htmlspecialchars($item['author']); ?></p>
                                    <p class="book-isbn">ISBN: <?php echo htmlspecialchars($item['isbn']); ?></p>

                                    <!-- Availability indicators -->
                                    <div class="availability-indicators">
                                        <div class="indicator"><i class="bi bi-check-circle-fill"></i> Click & Collect</div>
                                        <div class="indicator"><i class="bi bi-check-circle-fill"></i> Home Delivery</div>
                                    </div>

                                    <!-- Price and Quantity Controls -->
                                    <div class="price-quantity-row">
                                        <div class="quantity-control">
                                            <span>Qty:</span>
                                            <div class="quantity-wrapper" data-isbn="<?php echo htmlspecialchars($item['isbn']); ?>" data-price="<?php echo $item['price']; ?>">
                                                <button class="decrease-btn">−</button>
                                                <input type="number" name="qty" min="1" max="10" step="1" value="<?php echo (int)$item['qty']; ?>" class="qty-input">
                                                <button class="increase-btn">+</button>
                                            </div>
                                        </div>

                                        <div class="price-info">
                                            <span class="item-price">$<?php echo number_format($item['price'], 2); ?></span>
                                            <span class="item-subtotal">$<?php echo number_format($item['subtotal'], 2); ?></span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Remove Button -->
                                <a href="cart.php?action=remove&isbn=<?php echo urlencode($item['isbn']); ?>" class="remove-btn">
                                    <i class="bi bi-x"></i>
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="cart-footer-actions d-flex justify-content-between">
                    <form action="cart.php" method="POST" class="d-flex">
                        <input type="text" name="coupon_code" placeholder="Coupon Code" value="<?php echo isset($_SESSION['coupon_code']) ? htmlspecialchars($_SESSION['coupon_code']) : ''; ?>">
                        <button type="submit" name="apply_coupon" class="theme-btn">Apply</button>
                    </form>
                    <a href="cart.php?action=clear" class="theme-btn" onclick="return confirm('Are you sure you want to clear your cart?')">Clear Cart</a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="col-xl-3">
        <div class="table-responsive cart-summary">
            <table class="table">
                <thead>
                    <tr>
                        <th>Cart Total</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="d-flex justify-content-between">
                            <span class="sub-title">Subtotal:</span>
                            <span class="sub-price" id="cart-subtotal">$<?php echo number_format($totalPrice, 2); ?></span>
                        </td>
                    </tr>
                    <?php if ($discount > 0): ?>
                        <tr>
                            <td class="d-flex justify-content-between">
                                <span class="sub-title">Discount:</span>
                                <span class="sub-price discount" id="cart-discount">-$<?php echo number_format($discount, 2); ?></span>
                            </td>
                        </tr>
                    <?php endif; ?>
                    <tr>
                        <td class="d-flex justify-content-between">
                            <span class="sub-title">Shipping:</span>
                            <span class="sub-text">Free</span>
                        </td>
                    </tr>
                    <tr>
                        <td class="d-flex justify-content-between">
                            <span class="sub-title">Total:</span>
                            <span class="sub-price sub-price-total" id="cart-total">$<?php echo number_format($finalTotal, 2); ?></span>
                        </td>
                    </tr>
                </tbody>
            </table>
            <?php if (!empty($cartItems)): ?>
                <a href="checkout.php" class="checkout-action-btn">
                    <span>Proceed to Checkout</span>
                </a>
            <?php else: ?>
                <a href="/Booksy/shop.php" class="checkout-action-btn">
                    <span>Continue Shopping</span>
                    <i class="bi bi-arrow-right ms-2"></i>
                </a>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle quantity controls
        const quantityWrappers = document.querySelectorAll('.quantity-wrapper');
        quantityWrappers.forEach(wrapper => {
            const decreaseBtn = wrapper.querySelector('.decrease-btn');
            const increaseBtn = wrapper.querySelector('.increase-btn');
            const input = wrapper.querySelector('.qty-input');
            const isbn = wrapper.getAttribute('data-isbn');
            const price = parseFloat(wrapper.getAttribute('data-price'));

            decreaseBtn.addEventListener('click', function() {
                let value = parseInt(input.value);
                if (value > 1) {
                    value--;
                    input.value = value;
                    updateCart(isbn, value, wrapper);
                }
            });

            increaseBtn.addEventListener('click', function() {
                let value = parseInt(input.value);
                if (value < 10) {
                    value++;
                    input.value = value;
                    updateCart(isbn, value, wrapper);
                }
            });

            input.addEventListener('change', function() {
                let value = parseInt(input.value);
                if (value < 1) {
                    value = 1;
                    input.value = value;
                } else if (value > 10) {
                    value = 10;
                    input.value = value;
                }
                updateCart(isbn, value, wrapper);
            });
        });

        function updateCart(isbn, qty, wrapper) {
            // Update UI immediately for better user experience
            const row = wrapper.closest('.cart-item');
            const subtotalElement = row.querySelector('.item-subtotal');
            const price = parseFloat(wrapper.getAttribute('data-price'));
            const subtotal = price * qty;

            subtotalElement.textContent = '$' + subtotal.toFixed(2);

            // Send AJAX request to update cart
            const xhr = new XMLHttpRequest();
            xhr.open('GET', `cart.php?action=update&isbn=${isbn}&qty=${qty}&ajax=1`, true);
            xhr.responseType = 'json';
            xhr.onload = function() {
                if (xhr.status === 200 && xhr.response) {
                    const response = xhr.response;

                    // Update subtotal display
                    document.getElementById('cart-subtotal').textContent = '$' + response.totals.subtotal;

                    // Update discount if exists
                    const discountElement = document.getElementById('cart-discount');
                    if (discountElement) {
                        discountElement.textContent = '-$' + response.totals.discount;
                    }

                    // Update final total
                    document.getElementById('cart-total').textContent = '$' + response.totals.final;

                    // Update cart counter in header (if exists)
                    const cartCounter = document.getElementById('cartCounter');
                    if (cartCounter) {
                        // Calculate total items in cart
                        let totalItems = 0;
                        // You'd need to get this data from the server response
                        // For now we'll just set it to the current quantity
                        totalItems = qty;
                        cartCounter.textContent = totalItems;
                    }
                }
            };
            xhr.send();
        }
    });
</script>

<?php
$conn->close();
include 'Pages/footer.php';
?>