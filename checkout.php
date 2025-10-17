<?php
session_start();

// Redirect to cart if cart is empty
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header("Location: cart.php");
    exit();
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

// Get cart items details from database
$cartItems = [];
$totalPrice = isset($_SESSION['cart_subtotal']) ? $_SESSION['cart_subtotal'] : 0;
$discount = isset($_SESSION['discount']) ? $_SESSION['discount'] : 0;
$finalTotal = isset($_SESSION['cart_final_total']) ? $_SESSION['cart_final_total'] : $totalPrice;

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

// Process order if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['place_order'])) {
    $firstName = $_POST['user-first-name'] ?? '';
    $lastName = $_POST['user-last-name'] ?? '';
    $company = $_POST['company-name'] ?? '';
    $country = $_POST['country'] ?? '';
    $address1 = $_POST['user-address'] ?? '';
    $address2 = $_POST['user-address2'] ?? '';
    $city = $_POST['towncity'] ?? '';
    $state = $_POST['state'] ?? '';
    $zipCode = $_POST['zip_code'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $email = $_POST['email'] ?? '';
    $notes = $_POST['notes'] ?? '';
    $paymentMethod = $_POST['payment_method'] ?? '';
    $shippingMethod = $_POST['shipping_method'] ?? '';
    
    // Add shipping cost if applicable
    $shippingCost = 0;
    if ($shippingMethod === 'flat') {
        $shippingCost = 4.99;
    }
    
    $orderTotal = $finalTotal + $shippingCost;
    
    // Here you would save the order to your database
    // For now, we'll just set a success message
    $_SESSION['order_success'] = true;
    $_SESSION['order_total'] = $orderTotal;
    
    // Clear the cart
    $_SESSION['cart'] = [];
    if (isset($_SESSION['discount'])) {
        unset($_SESSION['discount']);
    }
    if (isset($_SESSION['coupon_code'])) {
        unset($_SESSION['coupon_code']);
    }
    $_SESSION['cart_subtotal'] = 0;
    $_SESSION['cart_final_total'] = 0;
    
    // Redirect to a thank you page
    header("Location: order-confirmation.php");
    exit();
}

?>

<style>
    /* Modern Checkout Styles */
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        color: #333;
        background-color: white;
    }

    .checkout-container {
        max-width: 1200px;
        margin: 30px auto;
        padding: 0 15px;
    }

    .checkout-header {
        text-align: center;
        margin-bottom: 30px;
    }

    .checkout-header h1 {
        font-size: 28px;
        margin-bottom: 10px;
        font-weight: 700;
    }

    .checkout-steps {
        display: flex;
        justify-content: center;
        margin-bottom: 30px;
        position: relative;
    }

    .checkout-step {
        display: flex;
        flex-direction: column;
        align-items: center;
        width: 150px;
        z-index: 1;
    }

    .step-number {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background-color: #e9ecef;
        color: #6c757d;
        display: flex;
        justify-content: center;
        align-items: center;
        font-weight: bold;
        margin-bottom: 8px;
    }

    .step-number.active {
        background-color: #5cb85c;
        color: white;
    }

    .step-number.completed {
        background-color: #5cb85c;
        color: white;
    }

    .step-text {
        font-size: 14px;
        text-align: center;
        color: #6c757d;
    }

    .step-text.active {
        color: #333;
        font-weight: 600;
    }

    .step-line {
        position: absolute;
        top: 16px;
        left: calc(25% + 16px);
        right: calc(25% + 16px);
        height: 2px;
        background-color: #e9ecef;
        z-index: 0;
    }

    .checkout-content {
        display: flex;
        flex-wrap: wrap;
        margin: 0 -15px;
    }

    .billing-details {
        flex: 1;
        min-width: 300px;
        padding: 0 15px;
        margin-bottom: 30px;
    }

    .order-summary {
        width: 400px;
        padding: 0 15px;
        margin-bottom: 30px;
    }

    .checkout-box {
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        padding: 25px;
        margin-bottom: 20px;
    }

    .checkout-box h2 {
        font-size: 18px;
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 1px solid #e9ecef;
        font-weight: 600;
    }

    .form-row {
        margin-bottom: 15px;
    }

    .form-label {
        display: block;
        margin-bottom: 5px;
        font-size: 14px;
        font-weight: 500;
    }

    .required::after {
        content: " *";
        color: #dc3545;
    }

    .form-control {
        width: 100%;
        height: 40px;
        padding: 8px 12px;
        font-size: 14px;
        border: 1px solid #ced4da;
        border-radius: 4px;
        transition: border-color 0.15s ease-in-out;
    }

    .form-control:focus {
        border-color: #80bdff;
        outline: 0;
        box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25);
    }

    .form-textarea {
        height: 100px;
        resize: vertical;
    }

    .form-check {
        display: flex;
        align-items: center;
        margin-bottom: 10px;
    }

    .form-check input {
        margin-right: 10px;
    }

    .form-check label {
        font-size: 14px;
        cursor: pointer;
    }

    .order-item {
        display: flex;
        align-items: center;
        margin-bottom: 15px;
    }

    .order-item-image {
        width: 60px;
        height: 60px;
        margin-right: 15px;
        background-color: #f8f9fa;
        border-radius: 4px;
        overflow: hidden;
    }

    .order-item-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .order-item-details {
        flex: 1;
    }

    .order-item-title {
        font-weight: 500;
        margin-bottom: 2px;
        font-size: 14px;
    }

    .order-item-quantity {
        color: #6c757d;
        font-size: 13px;
    }

    .order-item-price {
        font-weight: 600;
        text-align: right;
        min-width: 80px;
    }

    .order-total-line {
        display: flex;
        justify-content: space-between;
        margin-bottom: 10px;
        font-size: 14px;
    }

    .order-total-line.total {
        margin-top: 15px;
        padding-top: 15px;
        border-top: 1px solid #e9ecef;
        font-size: 18px;
        font-weight: 700;
    }

    .shipping-options {
        margin-top: 15px;
    }

    .payment-option {
        border: 1px solid #e9ecef;
        border-radius: 4px;
        padding: 15px;
        margin-bottom: 15px;
        transition: all 0.2s ease;
    }

    .payment-option.selected {
        border-color: #5cb85c;
        background-color: rgba(92, 184, 92, 0.05);
    }

    .payment-option-header {
        display: flex;
        align-items: center;
        cursor: pointer;
    }

    .payment-option-radio {
        margin-right: 10px;
    }

    .payment-option-content {
        padding-top: 15px;
        margin-top: 15px;
        border-top: 1px solid #e9ecef;
        font-size: 13px;
        color: #6c757d;
    }

    .payment-logos {
        display: flex;
        margin-top: 15px;
        gap: 10px;
    }

    .payment-logos img {
        height: 24px;
        opacity: 0.7;
    }

    .checkout-button {
        display: block;
        width: 100%;
        background-color: #4CAF50;
        color: white;
        border: none;
        padding: 12px 20px;
        font-size: 16px;
        font-weight: 600;
        border-radius: 4px;
        cursor: pointer;
        transition: background-color 0.3s;
        margin-top: 20px;
    }

    .checkout-button:hover {
        background-color: #45a049;
    }

    .checkout-button:disabled {
        background-color: #cccccc;
        cursor: not-allowed;
    }

    .terms-checkbox {
        margin-top: 15px;
        font-size: 13px;
    }

    .terms-link {
        color: #007bff;
        text-decoration: none;
    }

    .timer-bar {
        background-color: #fff3cd;
        padding: 10px;
        text-align: center;
        border-radius: 4px;
        margin-bottom: 20px;
        font-size: 14px;
        color: #856404;
    }

    .customer-login {
        margin-bottom: 20px;
        font-size: 14px;
    }

    .customer-login a {
        color: #007bff;
        text-decoration: none;
        font-weight: 500;
    }

    .testimonial {
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        padding: 20px;
        margin-top: 30px;
    }

    .testimonial-header {
        font-weight: 600;
        margin-bottom: 15px;
        font-size: 18px;
    }

    .testimonial-content {
        display: flex;
        align-items: center;
    }

    .testimonial-avatar {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        overflow: hidden;
        margin-right: 15px;
    }

    .testimonial-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .testimonial-text {
        flex: 1;
        font-size: 14px;
        font-style: italic;
        color: #6c757d;
    }

    @media (max-width: 768px) {
        .checkout-content {
            flex-direction: column;
        }
        .order-summary {
            width: 100%;
        }
    }
</style>

<div class="checkout-container">
    <!-- Shop Header/Logo -->
    <div class="checkout-header">
        <div class="shop-logo">
            <img src="/Booksy/Assets/logo.png" alt="Booksy" style="height: 40px;">
        </div>
        <h1>Checkout</h1>
    </div>

    <!-- Timer Bar -->
    <div class="timer-bar">
        Your order is reserved for: <strong>8 minutes 59 seconds</strong>
    </div>

    <!-- Checkout Steps -->
    <div class="checkout-steps">
        <div class="step-line"></div>
        
        <div class="checkout-step">
            <div class="step-number completed">1</div>
            <div class="step-text">Shopping Cart</div>
        </div>
        
        <div class="checkout-step">
            <div class="step-number active">2</div>
            <div class="step-text active">Shipping and Checkout</div>
        </div>
        
        <div class="checkout-step">
            <div class="step-number">3</div>
            <div class="step-text">Confirmation</div>
        </div>
    </div>

    <!-- Returning Customer -->
    <div class="customer-login">
        Returning customer? <a href="#">Click here to login</a>
    </div>

    <form action="" method="post">
        <div class="checkout-content">
            <!-- Billing Details -->
            <div class="billing-details">
                <div class="checkout-box">
                    <h2>Billing details</h2>
                    
                    <div class="form-row" style="display: flex; gap: 15px;">
                        <div style="flex: 1;">
                            <label class="form-label required" for="firstName">First name</label>
                            <input type="text" id="firstName" name="user-first-name" class="form-control" required>
                        </div>
                        <div style="flex: 1;">
                            <label class="form-label required" for="lastName">Last name</label>
                            <input type="text" id="lastName" name="user-last-name" class="form-control" required>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <label class="form-label" for="companyName">Company name (optional)</label>
                        <input type="text" id="companyName" name="company-name" class="form-control">
                    </div>
                    
                    <div class="form-row">
                        <label class="form-label required" for="country">Country / Region</label>
                        <select id="country" name="country" class="form-control" required>
                            <option value="">Select a country</option>
                            <option value="US">United States (US)</option>
                            <option value="CA">Canada</option>
                            <option value="UK">United Kingdom</option>
                            <option value="AU">Australia</option>
                        </select>
                    </div>
                    
                    <div class="form-row">
                        <label class="form-label required" for="streetAddress">Street address</label>
                        <input type="text" id="streetAddress" name="user-address" class="form-control" placeholder="House number and street name" required>
                    </div>
                    
                    <div class="form-row">
                        <input type="text" id="streetAddress2" name="user-address2" class="form-control" placeholder="Apartment, suite, unit, etc. (optional)">
                    </div>
                    
                    <div class="form-row">
                        <label class="form-label required" for="city">Town / City</label>
                        <input type="text" id="city" name="towncity" class="form-control" required>
                    </div>
                    
                    <div class="form-row">
                        <label class="form-label required" for="state">State</label>
                        <select id="state" name="state" class="form-control" required>
                            <option value="">Select a state</option>
                            <option value="NY">New York</option>
                            <option value="CA">California</option>
                            <option value="TX">Texas</option>
                            <option value="FL">Florida</option>
                        </select>
                    </div>
                    
                    <div class="form-row">
                        <label class="form-label required" for="zipCode">ZIP Code</label>
                        <input type="text" id="zipCode" name="zip_code" class="form-control" required>
                    </div>
                    
                    <div class="form-row">
                        <label class="form-label required" for="phone">Phone</label>
                        <input type="tel" id="phone" name="phone" class="form-control" required>
                    </div>
                    
                    <div class="form-row">
                        <label class="form-label required" for="email">Email address</label>
                        <input type="email" id="email" name="email" class="form-control" required>
                    </div>

                    <div class="form-check">
                        <input type="checkbox" id="createAccount" name="create_account" class="form-check-input">
                        <label for="createAccount">Create an account?</label>
                    </div>
                    
                    <div class="form-check">
                        <input type="checkbox" id="shipDifferent" name="ship_different" class="form-check-input">
                        <label for="shipDifferent">Ship to a different address?</label>
                    </div>
                    
                    <div class="form-row">
                        <label class="form-label" for="orderNotes">Order notes (optional)</label>
                        <textarea id="orderNotes" name="notes" class="form-control form-textarea" placeholder="Notes about your order, e.g. special notes for delivery."></textarea>
                    </div>
                </div>
            </div>
            
            <!-- Order Summary -->
            <div class="order-summary">
                <div class="checkout-box">
                    <h2>Your order</h2>
                    
                    <!-- Order Items -->
                    <?php foreach ($cartItems as $item): ?>
                    <div class="order-item">
                        <div class="order-item-image">
                            <img src="<?php echo htmlspecialchars($item['coverimg']); ?>" alt="<?php echo htmlspecialchars($item['title']); ?>">
                        </div>
                        <div class="order-item-details">
                            <div class="order-item-title"><?php echo htmlspecialchars($item['title']); ?></div>
                            <div class="order-item-quantity">× <?php echo $item['qty']; ?></div>
                        </div>
                        <div class="order-item-price">$<?php echo number_format($item['subtotal'], 2); ?></div>
                    </div>
                    <?php endforeach; ?>
                    
                    <!-- Order Totals -->
                    <div class="order-total-line">
                        <span>Subtotal</span>
                        <span>$<?php echo number_format($totalPrice, 2); ?></span>
                    </div>
                    
                    <?php if ($discount > 0): ?>
                    <div class="order-total-line">
                        <span>Discount</span>
                        <span style="color: #28a745;">-$<?php echo number_format($discount, 2); ?></span>
                    </div>
                    <?php endif; ?>
                    
                    <div class="order-total-line">
                        <span>Shipping</span>
                        <div class="shipping-options">
                            <div class="form-check">
                                <input type="radio" id="shippingFree" name="shipping_method" value="free" checked>
                                <label for="shippingFree">Free shipping</label>
                            </div>
                            <div class="form-check">
                                <input type="radio" id="shippingFlat" name="shipping_method" value="flat">
                                <label for="shippingFlat">Flat rate: $4.99</label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="order-total-line total">
                        <span>Total</span>
                        <span id="totalPrice">$<?php echo number_format($finalTotal, 2); ?></span>
                    </div>
                </div>
                
                <!-- Payment Methods -->
                <div class="checkout-box">
                    <div class="payment-option selected">
                        <div class="payment-option-header">
                            <input type="radio" id="paymentBank" name="payment_method" value="bank" checked class="payment-option-radio">
                            <label for="paymentBank">Direct bank transfer</label>
                        </div>
                        <div class="payment-option-content">
                            Make your payment directly into our bank account. Please use your Order ID as the payment reference. Your order will not be shipped until the funds have cleared in our account.
                        </div>
                    </div>
                    
                    <div class="payment-option">
                        <div class="payment-option-header">
                            <input type="radio" id="paymentChecks" name="payment_method" value="check" class="payment-option-radio">
                            <label for="paymentChecks">Check payments</label>
                        </div>
                    </div>
                    
                    <div class="terms-checkbox">
                        <input type="checkbox" id="terms" name="terms" required>
                        <label for="terms">I have read and agree to the website <a href="#" class="terms-link">terms and conditions</a> *</label>
                    </div>
                    
                    <button type="submit" name="place_order" class="checkout-button">Place order</button>
                    
                    <div class="payment-logos">
                        <img src="/Booksy/Assets/checkout/paypal.png" alt="PayPal">
                        <img src="/Booksy/Assets/checkout/visa.png" alt="Visa">
                        <img src="/Booksy/Assets/checkout/mastercard.png" alt="Mastercard">
                        <img src="/Booksy/Assets/checkout/GooglePay.png" alt="Google Pay">
                    </div>
                </div>
            </div>
        </div>
    </form>
    
    <!-- Testimonials -->
    <div class="testimonial">
        <div class="testimonial-header">What they are saying</div>
        <div class="testimonial-content">
            <div class="testimonial-avatar">
                <img src="/Booksy/Assets/testimonials/avatar.jpg" alt="Customer">
            </div>
            <div class="testimonial-text">
                The quality of the customer service and speed of delivery was just fantastic – I know I can count on Booksy every time!
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle shipping method changes
        const shippingOptions = document.querySelectorAll('input[name="shipping_method"]');
        const totalDisplay = document.getElementById('totalPrice');
        const baseTotal = <?php echo $finalTotal; ?>;
        
        shippingOptions.forEach(option => {
            option.addEventListener('change', function() {
                let shippingCost = 0;
                
                if (this.value === 'flat') {
                    shippingCost = 4.99;
                }
                
                const newTotal = baseTotal + shippingCost;
                totalDisplay.textContent = '$' + newTotal.toFixed(2);
            });
        });
        
        // Handle payment method selection
        const paymentOptions = document.querySelectorAll('.payment-option');
        const paymentRadios = document.querySelectorAll('input[name="payment_method"]');
        
        paymentRadios.forEach((radio, index) => {
            radio.addEventListener('change', function() {
                paymentOptions.forEach(option => {
                    option.classList.remove('selected');
                });
                
                paymentOptions[index].classList.add('selected');
            });
        });
        
        // Timer functionality (countdown)
        let minutes = 8;
        let seconds = 59;
        const timerElement = document.querySelector('.timer-bar strong');
        
        const countdownTimer = setInterval(function() {
            if (seconds === 0) {
                if (minutes === 0) {
                    clearInterval(countdownTimer);
                    timerElement.textContent = "Expired";
                    return;
                }
                minutes--;
                seconds = 59;
            } else {
                seconds--;
            }
            
            timerElement.textContent = minutes + " minutes " + seconds + " seconds";
        }, 1000);
    });
</script>

<?php
$conn->close();
?>