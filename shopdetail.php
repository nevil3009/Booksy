<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

$servername = "localhost";
$username = "root";
$password = "";
$database = "booksy";
$conn = new mysqli($servername, $username, $password, $database);



if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get ISBN from URL parameter
$isbn = isset($_GET['isbn']) ? $_GET['isbn'] : '';

if ($isbn == "") {
    header("Location: /Booksy/shop.php");  // Add full path
    exit();
}
// Fetch book details
// Fetch book details
$sql = "SELECT books.*, author.name AS author 
        FROM books 
        LEFT JOIN author ON books.authorid = author.authorid 
        WHERE books.isbn = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $isbn);
$stmt->execute();
$result = $stmt->get_result();

// Check if a book was found
// if ($result->num_rows == 0) {
//     die("Error: No book found with this ISBN.");
// }

// Fetch the book data
$book = $result->fetch_assoc();
// print_r($book);
$avgRating = 4.5; // Static average rating
$reviewCount = 3; // Static review count

$reviews = [
    [
        'user_name' => 'John Doe',
        'created_at' => '2024-02-15 14:30:00',
        'rating' => 5,
        'comment' => 'Excellent book! I couldn\'t put it down. The characters are well-developed and the plot is engaging.'
    ],
    [
        'user_name' => 'Jane Smith',
        'created_at' => '2024-02-10 09:15:00',
        'rating' => 4,
        'comment' => 'A very good read. The storyline is captivating, although I found some parts a bit slow.'
    ],
    [
        'user_name' => 'Robert Johnson',
        'created_at' => '2024-01-28 16:45:00',
        'rating' => 5,
        'comment' => 'One of the best books I\'ve read this year. The author\'s writing style is exceptional.'
    ]
];

include __DIR__ . '/Pages/header.php';
?>

<link rel="stylesheet" href="shopdetail.css">

<div class="page-navigation-wrapper">
    <div class="book1">
        <img src="/Booksy/Assets/shop-default/book1.png" alt="book">
    </div>
    <div class="book2">
        <img src="/Booksy/Assets/shop-default/book2.png" alt="book">
    </div>
    <div class="container">
        <div class="page-heading">
            <h1>Shop Details</h1>
            <div class="page-header">
                <ul class="custom-navigation">
                    <li><a href="/Booksy/index.php">Home</a></li>
                    <li><i class="bi bi-chevron-right"></i></li>
                    <li><a href="/Booksy/shop.php">Shop</a></li>
                    <li><i class="bi bi-chevron-right"></i></li>
                    <li><?php echo htmlspecialchars($book['title']); ?></li>
                </ul>
            </div>
        </div>
    </div>
</div>

<section class="shopdetails-section fix section-padding">
    <div class="container">
        <div class="shopdetails-items">
            <div class="row g-4">
                <div class="col-lg-5">
                    <div class="shopdetails-image">
                        <div class="tab-content">
                            <div id="thumb1" class="tab-pane show active" role="tabpanel">
                                <div class="shopdetails-thumb">
                                    <img src="<?php echo htmlspecialchars($book['coverimg']); ?>" alt="<?php echo htmlspecialchars($book['title']); ?>">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-7" style="padding-left: 30px;">
                    <div class="product-details-content">
                        <div class="product-title-wrapper">
                            <h2><?php echo htmlspecialchars($book['title']); ?></h2>
                            <h5>In Stock</h5>
                        </div>
                        <div class="product-author">
                            <h4>By: <?php echo htmlspecialchars($book['author'] ?? 'Unknown Author'); ?></h4>
                        </div>
                        <div class="product-rating">
                            <?php
                            for ($i = 1; $i <= 5; $i++) {
                                if ($i <= $avgRating) {
                                    echo '<i class="bi bi-star-fill"></i>';
                                } else {
                                    echo '<i class="bi bi-star"></i>';
                                }
                            }
                            ?>
                            <span>(<?php echo $reviewCount; ?> Customer Reviews)</span>
                        </div>
                        <!-- Truncated Description with "Read More" Link -->
                        <p class="shopdetail-review-text">
                            <?php
                            $description = $book['description'] ?? '';
                            $maxLength = 120; // Character limit

                            if (strlen($description) > $maxLength) {
                                echo htmlspecialchars(substr($description, 0, $maxLength)) . '... ';
                                echo '<a href="#" data-bs-toggle="modal" data-bs-target="#readMoreModal" class="read-more">Read More</a>';
                            } else {
                                echo htmlspecialchars($description);
                            }
                            ?>
                        </p>

                        <div class="product-price">
                            <h3>$<?php echo number_format($book['price'] ?? 0, 2); ?></h3>
                        </div>

                        <div class="cart-wrapper">
                            <div class="quantity-basket">
                                <p class="qty">
                                    <button class="qtyminus" aria-hidden="true">−</button>
                                    <input type="number" name="qty" id="qty2" min="1" max="10" step="1" value="1">
                                    <button class="qtyplus" aria-hidden="true">+</button>
                                </p>
                            </div>

                            <!-- Read More Button -->
                            <button type="button" class="theme-btn style-2" data-bs-toggle="modal" data-bs-target="#readMoreModal">
                                Read A Little
                            </button>
                            <div class="modal" id="readMoreModal" tabindex="-1" aria-labelledby="readMoreModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-body custom-modal-body">
                                            <button type="button" class="btn-close close-btn" data-bs-dismiss="modal" aria-label="Close"></button>
                                            <div class="readMoreBox">
                                                <h3 id="readMoreModalLabel"><?php echo htmlspecialchars($book['title']); ?></h3>
                                                <p><?php echo nl2br(htmlspecialchars($book['description'] ?? '')); ?></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Add to Cart & Icons -->
                            <a href="cart.php?action=add&isbn=<?php echo urlencode($book['isbn']); ?>&qty=" class="theme-btn add-to-cart-btn">
                                <i class="bi bi-basket-fill"></i> Add To Cart
                            </a>
                            <div class="icon-box">
                                <a href="wishlist.php?action=add&isbn=<?php echo urlencode($book['isbn']); ?>" class="icon">
                                    <i class="bi bi-heart"></i>
                                </a>
                                <a href="compare.php?action=add&isbn=<?php echo urlencode($book['isbn']); ?>" class="icon-2">
                                    <img src="/Booksy/Assets/shopdetail/shuffle.svg" alt="svg-icon">
                                </a>
                            </div>
                        </div>

                        <div class="book-detail-category-box">
                            <div class="book-detail-category-list">
                                <ul>
                                    <li>
                                        <span>ISBN:</span> <?php echo htmlspecialchars($book['isbn']); ?>
                                    </li>
                                    <li>
                                        <span>Category:</span> <?php echo htmlspecialchars($book['genres'] ?? 'Fiction'); ?>
                                    </li>
                                </ul>
                                <ul>
                                    <li>
                                        <span>Author:</span> <?php echo htmlspecialchars($book['author'] ?? 'Unknown Author'); ?>
                                    </li>
                                    <li>
                                        <span>Format:</span> Paperback
                                    </li>
                                </ul>
                                <ul>
                                    <li>
                                        <span>Total pages:</span> <?php echo htmlspecialchars($book['pages'] ?? 0); ?>
                                    </li>
                                    <li>
                                        <span>Language:</span> <?php echo htmlspecialchars($book['language'] ?? 'English'); ?>
                                    </li>
                                </ul>
                                <ul>
                                    <li>
                                        <span>Publish Date:</span> <?php echo htmlspecialchars($book['publishdate'] ?? ''); ?>
                                    </li>
                                    <li>
                                        <span>Country:</span> USA
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="info-box">
                            <div class="info-list">
                                <div class="info-column">
                                    <div class="info-item">
                                        <i class="check-icon">✓</i>
                                        Free shipping orders from $150
                                    </div>
                                    <div class="info-item">
                                        <i class="check-icon">✓</i>
                                        30 days exchange & return
                                    </div>
                                </div>
                                <div class="info-column">
                                    <div class="info-item">
                                        <i class="check-icon">✓</i>
                                        Mamaya Flash Discount: Starting at 30% Off
                                    </div>
                                    <div class="info-item">
                                        <i class="check-icon">✓</i>
                                        Safe & Secure online shopping
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="social-media-place-icon">
                            <h6>Also Available On:</h6>
                            <a href="https://www.customer.io/"><img src="/Booksy/Assets/shopdetail/cutomerio.png" alt="cutomer.io"></a>
                            <a href="https://www.amazon.com/"><img src="/Booksy/Assets/shopdetail/amazon.png" alt="amazon"></a>
                            <a href="https://www.dropbox.com/"><img src="/Booksy/Assets/shopdetail/dropbox.png" alt="dropbox"></a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="single-tab section-padding pb-0">
                <ul class="tab-navigation mb-5" role="tablist">
                    <li class="tab-item" role="presentation">
                        <a href="#description" data-bs-toggle="tab" class="tab-link ps-0 active" aria-selected="true" role="tab">
                            <h6>Description</h6>
                        </a>
                    </li>
                    <li class="tab-item" role="presentation">
                        <a href="#additional" data-bs-toggle="tab" class="tab-link" aria-selected="false" tabindex="-1" role="tab">
                            <h6>Additional Information</h6>
                        </a>
                    </li>
                    <li class="tab-item" role="presentation">
                        <a href="#review" data-bs-toggle="tab" class="tab-link" aria-selected="false" tabindex="-1" role="tab">
                            <h6>Reviews (<?php echo $reviewCount; ?>)</h6>
                        </a>
                    </li>
                </ul>
                <div class="tab-content">
                    <div id="description" class="tab-pane fade show active" role="tabpanel">
                        <div class="description-items">
                            <p><?php echo nl2br(htmlspecialchars($book['description'] ?? 'No description available.')); ?></p>
                        </div>
                    </div>
                    <div id="additional" class="tab-pane fade" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <tbody>
                                    <tr>
                                        <td class="text-1">Availability</td>
                                        <td class="text-2">Available</td>
                                    </tr>
                                    <tr>
                                        <td class="text-1">Categories</td>
                                        <td class="text-2"><?php echo htmlspecialchars($book['genres'] ?? 'Fiction'); ?></td>
                                    </tr>
                                    <tr>
                                        <td class="text-1">Publisher</td>
                                        <td class="text-2"><?php echo htmlspecialchars($book['publisher'] ?? 'Unknown Publisher'); ?></td>
                                    </tr>
                                    <tr>
                                        <td class="text-1">Publish Date</td>
                                        <td class="text-2"><?php echo htmlspecialchars($book['publishdate'] ?? ''); ?></td>
                                    </tr>
                                    <tr>
                                        <td class="text-1">Total Pages</td>
                                        <td class="text-2"><?php echo htmlspecialchars($book['pages'] ?? 0); ?></td>
                                    </tr>
                                    <tr>
                                        <td class="text-1">Format</td>
                                        <td class="text-2">Paperback</td>
                                    </tr>
                                    <tr>
                                        <td class="text-1">Country</td>
                                        <td class="text-2">USA</td>
                                    </tr>
                                    <tr>
                                        <td class="text-1">Language</td>
                                        <td class="text-2"><?php echo htmlspecialchars($book['language'] ?? 'English'); ?></td>
                                    </tr>
                                    <tr>
                                        <td class="text-1">Dimensions</td>
                                        <td class="text-2">5.5 x 8.5 inches</td>
                                    </tr>
                                    <tr>
                                        <td class="text-1">Weight</td>
                                        <td class="text-2">0.8 lbs</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div id="review" class="tab-pane fade" role="tabpanel">
                        <div class="review-items">
                            <?php foreach ($reviews as $review): ?>
                                <div class="review-wrap-area d-flex gap-4 mb-4">
                                    <div class="review-thumb">
                                        <img src="/Booksy/Assets/shopdetail/review.png" alt="img">
                                    </div>
                                    <div class="review-content">
                                        <div class="head-area d-flex flex-wrap gap-2 align-items-center justify-content-between">
                                            <div class="cont">
                                                <h5><a href="#"><?php echo htmlspecialchars($review['user_name']); ?></a></h5>
                                                <span><?php echo date('F j, Y \a\t g:i a', strtotime($review['created_at'])); ?></span>
                                            </div>
                                            <div class="star">
                                                <?php
                                                for ($i = 1; $i <= 5; $i++) {
                                                    if ($i <= $review['rating']) {
                                                        echo '<i class="bi bi-star-fill"></i>';
                                                    } else {
                                                        echo '<i class="bi bi-star"></i>';
                                                    }
                                                }
                                                ?>
                                            </div>
                                        </div>
                                        <p><?php echo htmlspecialchars($review['comment']); ?></p>
                                    </div>
                                </div>
                            <?php endforeach; ?>

                            <div class="review-title mt-5 py-15 mb-30">
                                <h4 style="display: flex;">Your Rating*</h4>
                                <div class="rate-now d-flex align-items-center">
                                    <p>Your Rating*</p>
                                    <div class="star rating-stars">
                                        <i class="bi bi-star" data-rating="1"></i>
                                        <i class="bi bi-star" data-rating="2"></i>
                                        <i class="bi bi-star" data-rating="3"></i>
                                        <i class="bi bi-star" data-rating="4"></i>
                                        <i class="bi bi-star" data-rating="5"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="review-form">
                                <form action="submit_review.php" method="POST">
                                    <input type="hidden" name="isbn" value="<?php echo htmlspecialchars($book['isbn']); ?>">
                                    <input type="hidden" name="rating" id="rating-value" value="0">
                                    <div class="row g-4">
                                        <div class="col-lg-6">
                                            <div class="form-clt">
                                                <span>Your Name*</span>
                                                <input type="text" name="name" id="name" placeholder="Your Name" required>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-clt">
                                                <span>Your Email*</span>
                                                <input type="email" name="email" id="email" placeholder="Your Email" required>
                                            </div>
                                        </div>
                                        <div class="col-lg-12">
                                            <div class="form-clt">
                                                <span>Message*</span>
                                                <textarea name="comment" id="message" placeholder="Write Message" required></textarea>
                                            </div>
                                        </div>
                                        <div class="col-lg-12">
                                            <div class="form-check d-flex gap-2 from-customradio">
                                                <input type="checkbox" class="form-check-input" name="terms" id="terms" required>
                                                <label class="form-check-label" for="terms">I accept your terms &amp; conditions</label>
                                            </div>
                                            <button type="submit" class="alt-theme-btn" style="display: flex;">Submit now</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Rating script -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Quantity buttons
        const qtyPlus = document.querySelector('.qtyplus');
        const qtyMinus = document.querySelector('.qtyminus');
        const qtyInput = document.querySelector('#qty2');

        if (qtyPlus && qtyMinus && qtyInput) {
            qtyPlus.addEventListener('click', function() {
                let currentVal = parseInt(qtyInput.value);
                if (!isNaN(currentVal) && currentVal < 10) {
                    qtyInput.value = currentVal + 1;
                }
            });

            qtyMinus.addEventListener('click', function() {
                let currentVal = parseInt(qtyInput.value);
                if (!isNaN(currentVal) && currentVal > 1) {
                    qtyInput.value = currentVal - 1;
                }
            });
        }

        // Rating stars
        const stars = document.querySelectorAll('.rating-stars i');
        const ratingInput = document.getElementById('rating-value');

        if (stars.length && ratingInput) {
            stars.forEach(star => {
                star.addEventListener('click', function() {
                    const rating = this.getAttribute('data-rating');
                    ratingInput.value = rating;

                    // Clear all stars
                    stars.forEach(s => s.classList.remove('bi-star-fill'));
                    stars.forEach(s => s.classList.add('bi-star'));

                    // Fill stars up to selected rating
                    for (let i = 0; i < rating; i++) {
                        stars[i].classList.remove('bi-star');
                        stars[i].classList.add('bi-star-fill');
                    }
                });
            });
        }
    });
    document.addEventListener('DOMContentLoaded', function() {
    // Connect the Add to Cart button with the quantity input
    const addToCartBtn = document.querySelector('.add-to-cart-btn');
    const qtyInput = document.querySelector('#qty2');
    
    if (addToCartBtn && qtyInput) {
        // Update href with current quantity on page load
        addToCartBtn.href = addToCartBtn.href + qtyInput.value;
        
        // Update href when quantity changes
        qtyInput.addEventListener('change', function() {
            let href = addToCartBtn.href;
            // Remove any previously set quantity
            href = href.replace(/&qty=\d*$/, '');
            // Add current quantity
            addToCartBtn.href = href + '&qty=' + qtyInput.value;
        });
        
        // Update when plus/minus buttons are clicked
        const qtyPlus = document.querySelector('.qtyplus');
        const qtyMinus = document.querySelector('.qtyminus');
        
        if (qtyPlus) {
            qtyPlus.addEventListener('click', function() {
                let href = addToCartBtn.href;
                href = href.replace(/&qty=\d*$/, '');
                addToCartBtn.href = href + '&qty=' + qtyInput.value;
            });
        }
        
        if (qtyMinus) {
            qtyMinus.addEventListener('click', function() {
                let href = addToCartBtn.href;
                href = href.replace(/&qty=\d*$/, '');
                addToCartBtn.href = href + '&qty=' + qtyInput.value;
            });
        }
    }
});
</script>

<?php
// Close database connection
$stmt->close();
$conn->close();

// Include footer
include 'Pages/footer.php';
?>