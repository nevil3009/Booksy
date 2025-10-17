<link rel="stylesheet" href="author-profile.css">
<?php include 'Pages/header.php'; ?>

<div class="page-navigation-wrapper">
    <div class="book1">
        <img src="/Booksy/AssetS/shop-default/book1.png" alt="book">
    </div>
    <div class="book2">
        <img src="/Booksy/AssetS/shop-default/book2.png" alt="book">
    </div>
    <div class="container">
        <div class="page-heading">
            <h1>Author Profile</h1>
            <div class="page-header">
                <ul class="custom-navigation">
                    <li><a href="/Booksy/index.php">Home</a></li>
                    <li><i class="bi bi-chevron-right"></i></li>
                    <li>Author Profile</li>
                </ul>
            </div>
        </div>
    </div>
</div><div class="author-fix">
    <div class="container py-4" style="max-width: 1595px;">
        <div class="author-card">
            <div class="author-image-wrapper">
                <div class="author-image-container">
                    <img src="/Booksy/Assets/details.png" alt="Author Wade Warren" class="author-image">
                </div>
            </div>
            <div class="author-info">
                <h5 class="author-label">Author: <span class="author-name">Wade Warren</span></h5>
                <p class="author-location">United States of America</p>
                <div class="social-links d-flex align-items-center">
                    <a href="https://www.facebook.com/" class="social-icon"><i class="bi bi-facebook"></i></a>
                    <a href="https://x.com/" class="social-icon"><i class="bi bi-twitter"></i></a>
                    <a href="https://www.youtube.com/" class="social-icon"><i class="bi bi-youtube"></i></a>
                    <a href="https://www.linkedin.com/" class="social-icon"><i class="bi bi-linkedin"></i></a>
                </div>
            </div>
            <p class="author-description">
                Morbi cursus enim in consequat suscipit. Quisque id dui ante. Praesent auctor sed velit ac aliquet.
                Morbi consectetur sem nec ipsum malesuada, ut gravida nisl molestie. Proin hendrerit ullamcorper
                dui.
            </p>
            <div class="stats-container">
                <div class="stat-item">
                    <span class="stat-number">4+</span>
                    <span class="stat-label">Books</span>
                </div>
                <div class="stat-divider"></div>
                <div class="stat-item">
                    <span class="stat-number">100+</span>
                    <span class="stat-label">Sales</span>
                </div>
                <div class="stat-divider"></div>
                <div class="stat-item">
                    <span class="stat-number">90+</span>
                    <span class="stat-label">Reviews</span>
                </div>
            </div>
        </div>
    </div>
</div>








<div class="container-fluid py-4">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0 featured-books-title">Books By Wade Warren</h2>
            <a href="#" class="btn btn-link text-decoration-none">Explore More →</a>
        </div>

        <div class="position-relative">
            <div class="unique-books-container">
                <div class="unique-books-wrapper">
                    <div class="unique-book-card card">
                        <div class="unique-book-image-container">
                            <img src="/Booksy/Assets/books/adventure.png" class="card-img-top unique-book-cover"
                                alt="Grow Flower">
                            <div class="unique-hover-dots">
                                <div class="unique-dot-action">
                                    <i class="bi bi-heart"></i>
                                </div>
                                <div class="unique-dot-action">
                                    <i class="bi bi-arrow-left-right"></i>
                                </div>
                                <div class="unique-dot-action">
                                    <i class="bi bi-eye"></i>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <small class="text-muted">Design Low Book</small>
                            <h5 class="card-title">The Hidden Mystery Behind</h5>
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <img src="/api/placeholder/32/32" class="unique-author-avatar" alt="Hawkins">
                                <small>Hawkins</small>
                            </div>
                            <div class="mb-2">
                                <i class="bi bi-star-fill unique-rating-stars"></i>
                                <i class="bi bi-star-fill unique-rating-stars"></i>
                                <i class="bi bi-star-fill unique-rating-stars"></i>
                                <i class="bi bi-star-fill unique-rating-stars"></i>
                                <i class="bi bi-star text-muted"></i>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">$29.00</h5>
                            </div>
                        </div>
                        <div class="card-footer bg-transparent border-0">
                            <button class="btn btn-outline-primary w-100">
                                <i class="bi bi-cart-plus"></i> Add To Cart
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'Pages/footer.php'; ?>