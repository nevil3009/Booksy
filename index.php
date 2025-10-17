<?php
// Start session
session_start();

// Redirect to login if not logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

// Connect to database
$conn = new mysqli("localhost", "root", "", "booksy");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Verify that user still exists
$userId = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT id FROM users WHERE id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    $_SESSION = [];
    session_destroy();
    header("Location: login.php");
    exit();
}
$stmt->close();

// Fetch top-rated book by genre
$categoryQuery = "
    SELECT b.genres, COUNT(b.isbn) AS book_count, 
           b.title, b.coverimg, b.rating
    FROM books b
    INNER JOIN (
        SELECT genres, MAX(rating) AS max_rating
        FROM books
        GROUP BY genres
    ) best_books
    ON b.genres = best_books.genres AND b.rating = best_books.max_rating
    GROUP BY b.genres
    ORDER BY book_count DESC
";

$categoryResult = $conn->query($categoryQuery);
$categories = [];
if ($categoryResult) {
    while ($row = $categoryResult->fetch_assoc()) {
        $categories[] = $row;
    }
}

// Fetch 6 random books
$bookQuery = "SELECT * FROM books ORDER BY RAND() LIMIT 6";
$bookResult = $conn->query($bookQuery);
$books = [];
if ($bookResult) {
    while ($row = $bookResult->fetch_assoc()) {
        $books[] = $row;
    }
}

// Close connection at the end
$conn->close();
?>


<link rel="stylesheet" href="index.css">
<?php include 'Pages/header.php'; ?>

<div class="main-section">
    <div class="container-first">
        <div class="row">
            <div class="col-12 col-sm-8">
                <div class="main-item">
                    <img src="Assets/book.png" class="book-image" alt="books">
                    <img src="Assets/frame.png" class="dotted-frame1" alt="frame">
                    <img src="Assets/frame-2.png" class="dotted-frame2" alt="frame2">
                    <img src="Assets/xstar.png" class="star" alt="star">
                    <img src="Assets/frame-shape.png" class="plane" alt="plane">
                    <img src="Assets/bg-shape.png" class="rounded-shape" alt="rounded-shape">
                </div>
                <div class="main-content" style="position:absolute; bottom: 270px;">
                    <h6 class="discount-text">Up To 30% Off</h6>
                    <h1 class="main-heading">Get Your New Book
                        <br>
                        With The Best Price
                    </h1>
                    <button type="button" class="shop-now-btn"
                        style="color: white; background-color:#036280;border:1px;font-size: 1.3rem;;font-weight: bold;">
                        Shop Now <span class="arrow">&rarr;</span>
                    </button>
                </div>
            </div>
            <div class="col-12 col-sm-4">
                <img src="Assets/hero-girl.png" class="girl" alt="girl-image">
            </div>
        </div>
    </div>
</div>

<div class="features-section">
    <div class="container" style="background-color: #D0E1E7; border-radius: 30px; max-width: 1500px;">
        <div class="row">
            <div class="col-md-3">
                <div class="feature-card">
                    <div class="feature-content">
                        <div class="icon-wrapper">
                            <i class="bi bi-truck"></i>
                        </div>
                        <div class="text-wrapper">
                            <h5 class="feature-title">Return & Refund</h5>
                            <p class="feature-description">Money back guarantee</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="feature-card">
                    <div class="feature-content">
                        <div class="icon-wrapper">
                            <i class="bi bi-shield-check"></i>
                        </div>
                        <div class="text-wrapper">
                            <h5 class="feature-title">Secure Payment</h5>
                            <p class="feature-description">30% off by subscribing</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="feature-card">
                    <div class="feature-content">
                        <div class="icon-wrapper">
                            <i class="bi bi-headset"></i>
                        </div>
                        <div class="text-wrapper">
                            <h5 class="feature-title">Quality Support</h5>
                            <p class="feature-description">Always online 24/7</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="feature-card">
                    <div class="feature-content">
                        <div class="icon-wrapper">
                            <i class="bi bi-tag"></i>
                        </div>
                        <div class="text-wrapper">
                            <h5 class="feature-title">Daily Offers</h5>
                            <p class="feature-description">20% off by subscribing</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="categories-section">
    <div class="container">
        <h2 class="section-title">Top Categories Book</h2>
        <div class="scroll-container">
            <div class="category-wrapper">
                <?php if (count($categories) > 0): ?>
                    <?php foreach ($categories as $row): ?>
                        <div class="category-item">
                            <div class="category-circle">
                                <div class="rotating-border"></div>
                                <div class="category-number"><?php echo $row['book_count']; ?></div> <!-- Book count -->
                                <img src="<?php echo $row['coverimg']; ?>" alt="<?php echo $row['genres']; ?> Book" class="book-cover">
                            </div>
                            <h5 class="category-title"><?php echo ucfirst($row['genres']); ?></h5>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-center">No categories found.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>


<div class="featured-books-container mt-5 mb-5">
    <!-- Section Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="featured-title">📚 Featured Books</h2>
        <a href="shop.php" class="explore-btn">Explore More →</a>
    </div>

    <div class="row">
        <?php if (count($books) > 0): ?>
            <?php foreach ($books as $row): ?>
                <div class="col-xl-2 col-lg-2 col-md-4 col-sm-6 col-12 book-column">
                    <div class="book-card">
                        <div class="book-thumb">
                            <a href="shopdetail.php?isbn=<?php echo $row['isbn']; ?>">
                                <img src="<?php echo $row['coverimg']; ?>" alt="<?php echo $row['title']; ?>">
                            </a>
                            <ul class="book-icons">
                                <li><a href="#"><i class="bi bi-heart"></i></a></li>
                                <li><a href="#"><i class="bi bi-arrow-left-right"></i></a></li>
                                <li><a href="shopdetail.php?isbn=<?php echo $row['isbn']; ?>"><i class="bi bi-eye"></i></a></li>
                            </ul>
                        </div>
                        <div class="book-content">
                            <h3>
                                <a href="shopdetail.php?isbn=<?php echo $row['isbn']; ?>">
                                    <?php echo strlen($row['title']) > 30 ? substr($row['title'], 0, 27) . '...' : $row['title']; ?>
                                </a>
                            </h3>
                            <ul class="price-list">
                                <li>$<?php echo number_format($row['price'], 2); ?></li>
                                <li class="rating">⭐ <?php echo $row['rating']; ?></li>
                            </ul>
                            <a href="shopdetail.php?isbn=<?php echo $row['isbn']; ?>" class="theme-btn">View Details</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="text-center">No books available.</p>
        <?php endif; ?>
    </div>
</div>


<div class="container-fluid-promo-banner">
    <div class="promo-banner-section">
        <div class="banner-wrapper">
            <div class="books-section">
                <!-- <img src="/Booksy/Assets/book-shape.png" alt="Featured Books" class="books-collage"> -->
            </div>

            <div class="content-section">
                <h2 class="promo-title">Get 25% Discount<br>On All Books</h2>
                <a href="#" class="shop-now-btn">
                    Shop Now
                    <i class="bi bi-arrow-right"></i>
                </a>
            </div>

            <div class="student-section">
                <img src="/Booksy/Assets/girl-shape-2.png" alt="Student Reading" class="student-image">
            </div>
        </div>
    </div>
</div>


<div class="container py-5">
    <h2 class="section-title">What Our Client Say</h2>

    <div class="testimonial-carousel">
        <div class="testimonial-track">

            <div class="testimonial-card">
                <img src="/Booksy/Assets/shape.svg" alt="Shape" class="card-shape">
                <p class="testimonial-content">
                    From the very first chapter, the authors engage readers with inspiring stories and practical
                    insights. Benjamin Zander's experiences as a conductor bring a unique perspective to leadership.
                </p>
                <div class="orange-line"></div>
                <div class="profile-section">
                    <img src="/Booksy/Assets/02.jpg" alt="Profile" class="profile-img">
                    <div class="profile-info">
                        <h5>Ronald Richards</h5>
                        <p>Marketing Coordinator</p>
                        <div class="rating">
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star"></i>
                            <i class="bi bi-star"></i>
                        </div>
                    </div>
                </div>
                <img src="/Booksy/Assets/logo2.png" alt="Amazon" class="company-logo">
            </div>

            <div class="testimonial-card">
                <img src="/Booksy/Assets/shape.svg" alt="Shape" class="card-shape">
                <p class="testimonial-content">
                    One of the most powerful takeaways from this book is the emphasis on adopting a mindset of
                    abundance and possibility. The idea that we can choose to see opportunities rather than
                    limitations is a game-changer.
                </p>
                <div class="orange-line"></div>
                <div class="profile-section">
                    <img src="/Booksy/Assets/02.jpg" alt="Profile" class="profile-img">
                    <div class="profile-info">
                        <h5>Ronald Richards</h5>
                        <p>Marketing Coordinator</p>
                        <div class="rating">
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star"></i>
                            <i class="bi bi-star"></i>
                        </div>
                    </div>
                </div>
                <img src="/Booksy/Assets/logo2.png" alt="Envato" class="company-logo">

            </div>

            <div class="testimonial-card">
                <img src="/Booksy/Assets/shape.svg" alt="Shape" class="card-shape">
                <p class="testimonial-content">
                    The idea that we can choose to see opportunities rather than limitations is a game-changer. The
                    book encourages readers to step out of their comfort zones and embrace a more positive outlook
                    on life.
                </p>
                <div class="orange-line"></div>
                <div class="profile-section">
                    <img src="/Booksy/Assets/02.jpg" alt="Profile" class="profile-img">
                    <div class="profile-info">
                        <h5>Dianne Russell</h5>
                        <p>Project Manager</p>
                        <div class="rating">
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star"></i>
                            <i class="bi bi-star"></i>
                        </div>
                    </div>
                </div>
                <img src="/Booksy/Assets/logo2.png" alt="Company" class="company-logo">
            </div>


            <div class="testimonial-card">
                <img src="/Booksy/Assets/shape.svg" alt="Shape" class="card-shape">
                <p class="testimonial-content">
                    From the very first chapter, the authors engage readers with inspiring stories and practical
                    insights. Benjamin Zander's experiences as a conductor bring a unique perspective to leadership.
                </p>
                <div class="orange-line"></div>
                <div class="profile-section">
                    <img src="/Booksy/Assets/02.jpg" alt="Profile" class="profile-img">
                    <div class="profile-info">
                        <h5>Ronald Richards</h5>
                        <p>Marketing Coordinator</p>
                        <div class="rating">
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star"></i>
                            <i class="bi bi-star"></i>
                        </div>
                    </div>
                </div>
                <img src="/Booksy/Assets/logo2.png" alt="Amazon" class="company-logo">
            </div>

            <div class="testimonial-card">
                <img src="/Booksy/Assets/shape.svg" alt="Shape" class="card-shape">
                <p class="testimonial-content">
                    One of the most powerful takeaways from this book is the emphasis on adopting a mindset of
                    abundance and possibility. The idea that we can choose to see opportunities rather than
                    limitations is a game-changer.
                </p>
                <div class="orange-line"></div>
                <div class="profile-section">
                    <img src="/Booksy/Assets/02.jpg" alt="Profile" class="profile-img">
                    <div class="profile-info">
                        <h5>Ronald Richards</h5>
                        <p>Marketing Coordinator</p>
                        <div class="rating">
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star"></i>
                            <i class="bi bi-star"></i>
                        </div>
                    </div>
                </div>
                <img src="/Booksy/Assets/logo2.png" alt="Envato" class="company-logo">
            </div>

            <div class="testimonial-card">
                <img src="/Booksy/Assets/shape.svg" alt="Shape" class="card-shape">
                <p class="testimonial-content">
                    The idea that we can choose to see opportunities rather than limitations is a game-changer. The
                    book encourages readers to step out of their comfort zones and embrace a more positive outlook
                    on life.
                </p>
                <div class="orange-line"></div>
                <div class="profile-section">
                    <img src="/Booksy/Assets/02.jpg" alt="Profile" class="profile-img">
                    <div class="profile-info">
                        <h5>Dianne Russell</h5>
                        <p>Project Manager</p>
                        <div class="rating">
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star"></i>
                            <i class="bi bi-star"></i>
                        </div>
                    </div>
                </div>
                <img src="/Booksy/Assets/logo2.png" alt="Company" class="company-logo">
            </div>
        </div>
    </div>
</div>



<!-- <div class="container">
    <h2 class="featured-title">Featured Author</h2>
    <p class="subtitle">"Sometimes, when hunger and thirst come upon us, even before the first mouthfuls, we find
        ourselves at ease."</p>

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
</div> -->
<button class="scroll-to-top" id="scrollBtn">↑</button>
</section>

<script>
    const scrollButton = document.getElementById('scrollBtn');


    scrollButton.style.display = 'none';


    window.addEventListener('scroll', () => {

        if (document.documentElement.scrollTop > 200 || document.body.scrollTop > 200) {
            scrollButton.style.display = 'block';
        } else {
            scrollButton.style.display = 'none';
        }
    });


    scrollButton.addEventListener('click', () => {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });
</script>
<?php include 'Pages/footer.php'; ?>