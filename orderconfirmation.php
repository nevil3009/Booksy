<?php
session_start();

// Redirect if not coming from a successful order
if (!isset($_SESSION['order_success']) || $_SESSION['order_success'] !== true) {
    header("Location: shop.php");
    exit();
}

// Get order details from session
$orderTotal = isset($_SESSION['order_total']) ? $_SESSION['order_total'] : 0;

// Generate a random order number
$orderNumber = "BK" . date('Ymd') . rand(1000, 9999);

// Clear the success flag but keep the order info for this page view
$_SESSION['order_success'] = false;

include 'Pages/header.php';
?>

<link rel="stylesheet" href="checkout.css">

<div class="page-navigation-wrapper">
    <div class="book1">
        <img src="/Booksy/Assets/shop-default/book1.png" alt="book">
    </div>
    <div class="book2">
        <img src="/Booksy/Assets/shop-default/book2.png" alt="book">
    </div>
    <div class="container">
        <div class="page-heading">
            <h1>Order Confirmation</h1>
            <div class="page-header">
                <ul class="custom-navigation">
                    <li><a href="/Booksy/index.php">Home</a></li>
                    <li><i class="bi bi-chevron-right"></i></li>
                    <li>Order Confirmation</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<style>
    .confirmation-container {
        max-width: 800px;
        margin: 50px auto;
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 20px rgba(0,0,0,0.1);
        padding: 40px;
        text-align: center;
    }
    
    .confirmation-icon {
        font-size: 70px;
        color: #28a745;
        margin-bottom: 20px;
    }
    
    .confirmation-title {
        font-size: 28px;
        font-weight: 600;
        margin-bottom: 15px;
        color: #333;
    }
    
    .confirmation-message {
        font-size: 16px;
        color: #666;
        margin-bottom: 30px;
    }
    
    .order-details {
        background-color: #f8f9fa;
        padding: 20px;
        border-radius: 8px;
        margin: 30px 0;
        text-align: left;
    }
    
    .detail-row {
        display: flex;
        justify-content: space-between;
        padding: 10px 0;
        border-bottom: 1px solid #eee;
    }
    
    .detail-row:last-child {
        border-bottom: none;
    }
    
    .action-buttons {
        display: flex;
        justify-content: center;
        gap: 15px;
        margin-top: 30px;
    }
    
    .action-btn {
        display: inline-block;
        padding: 12px 25px;
        border-radius: 4px;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s;
    }
    
    .primary-btn {
        background-color: #5f76e8;
        color: white;
    }
    
    .primary-btn:hover {
        background-color: #4c61c3;
        color: white;
    }
    
    .secondary-btn {
        background-color: #f8f9fa;
        color: #333;
        border: 1px solid #ddd;
    }
    
    .secondary-btn:hover {
        background-color: #e9ecef;
        color: #333;
    }
    
    .confetti {
        position: fixed;
        width: 10px;
        height: 10px;
        background-color: #f0f;
        opacity: 0;
        top: -10px;
        animation: confetti 5s ease-in-out forwards;
    }
    
    @keyframes confetti {
        0% {
            transform: translateY(0) rotate(0deg);
            opacity: 1;
        }
        100% {
            transform: translateY(100vh) rotate(720deg);
            opacity: 0;
        }
    }
</style>

<div class="confirmation-container">
    <i class="bi bi-check-circle-fill confirmation-icon"></i>
    <h2 class="confirmation-title">Thank You For Your Order!</h2>
    <p class="confirmation-message">Your order has been received and is now being processed.</p>
    
    <div class="order-details">
        <div class="detail-row">
            <strong>Order Number:</strong>
            <span><?php echo $orderNumber; ?></span>
        </div>
        <div class="detail-row">
            <strong>Date:</strong>
            <span><?php echo date('F j, Y'); ?></span>
        </div>
        <div class="detail-row">
            <strong>Email:</strong>
            <span>We've sent a confirmation to your email</span>
        </div>
        <div class="detail-row">
            <strong>Total:</strong>
            <span>$<?php echo number_format($orderTotal, 2); ?></span>
        </div>
        <div class="detail-row">
            <strong>Payment Method:</strong>
            <span>Direct Bank Transfer</span>
        </div>
    </div>
    
    <p>We'll send you a shipping confirmation email once your order has shipped.</p>
    
    <div class="action-buttons">
        <a href="/Booksy/shop.php" class="action-btn primary-btn">
            Continue Shopping
        </a>
        <a href="/Booksy/profile.php" class="action-btn secondary-btn">
            Track My Order
        </a>
    </div>
</div>

<script>
    // Create confetti effect
    function createConfetti() {
        const colors = ['#5f76e8', '#ff6b6b', '#20c997', '#ffd166', '#ef476f'];
        
        for (let i = 0; i < 100; i++) {
            const confetti = document.createElement('div');
            confetti.className = 'confetti';
            
            // Random position
            confetti.style.left = Math.random() * 100 + 'vw';
            
            // Random color
            confetti.style.backgroundColor = colors[Math.floor(Math.random() * colors.length)];
            
            // Random size
            const size = Math.random() * 10 + 5;
            confetti.style.width = size + 'px';
            confetti.style.height = size + 'px';
            
            // Random animation duration
            confetti.style.animationDuration = Math.random() * 3 + 2 + 's';
            
            // Append to body
            document.body.appendChild(confetti);
            
            // Remove after animation
            setTimeout(() => {
                confetti.remove();
            }, 5000);
        }
    }
    
    // Run confetti on page load
    window.onload = createConfetti;
</script>

<?php include 'Pages/footer.php'; ?>