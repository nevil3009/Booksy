<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
    exit();
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Booksy Header</title>
  <link href="/Booksy/Style/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.5/font/bootstrap-icons.min.css"
    rel="stylesheet" />
  <link rel="stylesheet" href="/Booksy/Style/header.css" />
  <link rel="stylesheet" href="/Booksy/Style/footer.css">

  <style>
    .cart-icon-container {
      position: relative;
    }

    .cart-counter {
      position: absolute;
      top: -10px;
      right: -10px;
      background-color: #ff6b6b;
      color: white;
      border-radius: 50%;
      width: 22px;
      height: 22px;
      display: flex;
      justify-content: center;
      align-items: center;
      font-size: 12px;
      font-weight: bold;
      transition: all 0.3s ease;
    }
  </style>
</head>

<body>
  <div class="header-top-1">
    <div class="container-fluid">
      <div class="d-flex justify-content-between align-items-center">
        <div class="w-100 text-center contact-info">
          <ul class="d-flex justify-content-center list-unstyled mb-0 ml-3">
            <li class="d-flex align-items-center me-3">
              <i class="bi bi-telephone"></i>
              <a href="tel:+91-1234567890" class="text-white text-decoration-none ms-1">+91-1234567890</a>
            </li>
            <li class="divider"></li>
            <li class="d-flex align-items-center mx-3">
              <i class="bi bi-envelope"></i>
              <a href="mailto:info@example.com" class="text-white text-decoration-none ms-1">info@example.com</a>
            </li>
            <li class="divider"></li>
            <li class="d-flex align-items-center ms-3">
              <i class="bi bi-clock"></i>
              <span class="text-white ms-1">Sunday - Fri: 9 AM - 6 PM</span>
            </li>
          </ul>
        </div>
        <div class="user-interaction">
          <ul class="list-unstyled d-flex align-items-center justify-content-end m-0">
            <li class="d-flex align-items-center me-3">
              <i class="bi bi-chat-dots text-white me-2"></i>
              <a href="#" class="text-white text-decoration-none">LiveChat</a>
            </li>
            <li class="d-flex align-items-center me-3">
              <i class="bi bi-person text-white me-2"></i>
              <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true): ?>
                <a href="/Booksy/logout.php" class="text-white text-decoration-none">Logout</a>
              <?php else: ?>
                <a href="/Booksy/login.php" class="text-white text-decoration-none">Login</a>
              <?php endif; ?>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>
  <header class="container-fluid">
    <div class="row">
      <div class="col-md-4 d-flex text-center">
        <a href="#" class="logo">
          <img src="/Booksy/Assets/booksy.png" alt="Logo" class="img-fluid" />
        </a>
      </div>
      <div class="col-md-4 d-flex align-items-center justify-content-center">
        <nav class="navbar navbar-expand-md">
          <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation"></button>
          <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav mx-auto">
              <li class="nav-item dropdown">
                <a class="nav-link" href="/Booksy/index.php">Home</a>
              </li>
              <li class="nav-item dropdown">
                <a class="nav-link" href="/Booksy/shop.php">Shop</a>
              </li>
              <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="#">Page</a>
                <ul class="dropdown-menu">
                  <li><a class="dropdown-item" href="/Booksy/about-us.php">About Us</a></li>
                  <li class="dropdown-hover">
                    <a class="dropdown-item d-flex justify-content-between align-items-center" href="#">
                      Author
                      <i class="bi bi-chevron-right"></i>
                    </a>
                  </li>
                  <li><a class="dropdown-item" href="#">Faq's</a></li>
                </ul>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="/Booksy/contact.php">Contact</a>
              </li>
            </ul>
          </div>
        </nav>
      </div>
      <div class="col-md-4 d-flex align-items-center justify-content-end">
        <div class="d-flex align-items-center">
          <div class="me-3 icon-container">
            <a href="/Booksy/wishlist.php">
              <i class="bi bi-heart"></i>
            </a>
          </div>

          <div class="me-3 icon-container cart-icon-container">
            <a href="/Booksy/cart.php">
              <i class="bi bi-cart"></i>
              <span class="cart-counter" id="cartCounter">0</span>
            </a>
          </div>

          <div class="icon-container" onclick="toggleSidebar()">
            <i class="bi bi-layout-text-sidebar-reverse"></i>
          </div>

          <div class="contact-sidebar" id="contactSidebar">
            <div class="sidebar-content">
              <div class="sidebar-header">
                <div class="logo">
                  <img src="/Booksy/Assets/booksy-removebg.png" alt="booksy" class="sidebar-logo">
                </div>
                <button class="close-btn" onclick="closeSidebar()">×</button>
              </div>

              <p class="sidebar-description">
                Nullam dignissim, ante scelerisque the is euismod fermentum odio sem semper the is erat, a feugiat leo
                urna eget eros. Duis Aenean a imperdiet risus.
              </p>

              <div class="contact-info-section">
                <h3>Contact Info</h3>

                <div class="contact-items">
                  <div class="contact-item">
                    <i class="bi bi-geo-alt"></i>
                    <span>Main Street, Melbourne, Australia</span>
                  </div>

                  <div class="contact-item">
                    <i class="bi bi-envelope"></i>
                    <span>info@example.com</span>
                  </div>

                  <div class="contact-item">
                    <i class="bi bi-clock"></i>
                    <span>Mod-Friday, 09am -05pm</span>
                  </div>

                  <div class="contact-item">
                    <i class="bi bi-telephone"></i>
                    <span>+11002345909</span>
                  </div>
                </div>
              </div>

              <button class="quote-btn">
                Get A Quote
                <i class="bi bi-arrow-right"></i>
              </button>

              <div class="social-links">
                <a href="#"><i class="bi bi-facebook"></i></a>
                <a href="#"><i class="bi bi-twitter"></i></a>
                <a href="#"><i class="bi bi-youtube"></i></a>
                <a href="#"><i class="bi bi-linkedin"></i></a>
              </div>
            </div>
          </div>

          <script>
            function toggleSidebar() {
              const sidebar = document.querySelector('.contact-sidebar');
              const overlay = document.querySelector('.sidebar-overlay');
              sidebar.classList.toggle('active');
              overlay.classList.toggle('active');
            }

            function closeSidebar() {
              const sidebar = document.querySelector('.contact-sidebar');
              const overlay = document.querySelector('.sidebar-overlay');
              sidebar.classList.remove('active');
              overlay.classList.remove('active');
            }

            document.addEventListener('click', (e) => {
              const sidebar = document.querySelector('.contact-sidebar');
              const overlay = document.querySelector('.sidebar-overlay');
              if (e.target === overlay) {
                sidebar.classList.remove('active');
                overlay.classList.remove('active');
              }
            });

            // Function to update cart counter
            function updateCartCounter() {
              // Get cart items from session storage or cookies
              let cartItems = JSON.parse(localStorage.getItem('booksyCartItems')) || [];
              let counter = document.getElementById('cartCounter');

              // Update the counter text with the number of items
              counter.textContent = cartItems.length;

              // If cart is empty, you might want to hide the counter
              if (cartItems.length === 0) {
                counter.style.display = 'none';
              } else {
                counter.style.display = 'flex';

                // Optional: animate the counter when it changes
                counter.classList.add('pulse');
                setTimeout(() => {
                  counter.classList.remove('pulse');
                }, 300);
              }
            }

            // Call this function on page load
            document.addEventListener('DOMContentLoaded', function() {
              updateCartCounter();
            });

            // Also update the counter when cart changes
            window.addEventListener('storage', function(e) {
              if (e.key === 'booksyCartItems') {
                updateCartCounter();
              }
            });
          </script>
        </div>
      </div>
    </div>
  </header>
  <div class="sidebar-overlay"></div>
</body>

</html>