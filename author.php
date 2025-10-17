<link rel="stylesheet" href="author.css">
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
            <h1>Author</h1>
            <div class="page-header">
                <ul class="custom-navigation">
                    <li><a href="/Booksy/index.php">Home</a></li>
                    <li><i class="bi bi-chevron-right"></i></li>
                    <li>Author</li>
                </ul>
            </div>
        </div>
    </div>
</div>


<div class="container">
    <h2 class="featured-title">Featured Author</h2>
    <p class="subtitle">"Sometimes, when hunger and thirst come upon us, even before the first mouthfuls, we find ourselves at ease."</p>

    <div class="authors-container">
        <div class="author-card">
            <div class="author-image-container">
                <img src="/Booksy/Assets/kalu.jpg" alt="Author" class="author-image">
                <img src="/Booksy/Assets/authorshape.png" alt="frame" class="laurel-frame">
            </div>
            <h3 class="author-name">Leslie Alexander</h3>
            <p class="books-count">05 Published Books</p>
        </div>
    </div>
</div>




<div class="container-fluid py-4">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0 featured-books-title">Top Selling Books</h2>
            <a href="#" class="btn btn-link text-decoration-none">Explore More →</a>
        </div>

        <div class="position-relative">
            <div class="unique-books-container">
                <div class="unique-books-wrapper">
                    <div class="unique-book-card card">
                        <div class="unique-book-image-container">
                            <img src="/Booksy/Assets/books/adventure.png" class="card-img-top unique-book-cover" alt="Grow Flower">
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
