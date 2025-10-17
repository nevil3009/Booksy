<link rel="stylesheet" href="shop.css">
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
            <h1>Shop Default</h1>
            <div class="page-header">
                <ul class="custom-navigation">
                    <li><a href="/Booksy/index.php">Home</a></li>
                    <li><i class="bi bi-chevron-right"></i></li>
                    <li>Shop Default</li>
                </ul>
            </div>
        </div>
    </div>
</div>


<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "booksy";
$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get selected genre from URL parameter
$selectedGenre = isset($_GET['genre']) ? $_GET['genre'] : '';

// Get price filter parameters
$minPrice = isset($_GET['min_price']) ? (int)$_GET['min_price'] : 0;  // Changed from 100 to 0
$maxPrice = isset($_GET['max_price']) ? (int)$_GET['max_price'] : 100;

// Get selected ratings filter
$selectedRatings = isset($_GET['ratings']) ? $_GET['ratings'] : [];

// Check if "View More" was clicked and how many categories to show
$showAllCategories = isset($_GET['show_all_categories']) && $_GET['show_all_categories'] == 1;
$categoriesPerPage = 5;

// Fetch all genres from the database with book counts
$genreQuery = "SELECT genres, COUNT(*) as book_count FROM books 
               WHERE genres IS NOT NULL AND genres != '' 
               GROUP BY genres 
               ORDER BY genres ASC";
$genreResult = $conn->query($genreQuery);

$allGenres = array();
while ($genreRow = $genreResult->fetch_assoc()) {
    $allGenres[] = $genreRow;
}

// Determine how many genres to display
$totalGenres = count($allGenres);
$displayedGenres = $showAllCategories ? $totalGenres : $categoriesPerPage;

// Pagination settings for books
$booksPerPage = 12;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$startFrom = ($page - 1) * $booksPerPage;

// Build the WHERE clause for genre, price, and rating filtering
$whereClause = ' WHERE 1=1';
$params = array();
$paramTypes = '';

// Add genre filter if selected
if (!empty($selectedGenre)) {
    $whereClause .= " AND genres = ?";
    $params[] = $selectedGenre;
    $paramTypes .= 's';
}

// Add price filter
$whereClause .= " AND price BETWEEN ? AND ?";
$params[] = $minPrice;
$params[] = $maxPrice;
$paramTypes .= 'dd'; // double for price values

// Add rating filter if selected
if (!empty($selectedRatings)) {
    $ratingConditions = [];
    foreach ($selectedRatings as $rating) {
        // Convert rating to integer to ensure proper comparison
        $ratingVal = (int)$rating;

        // For each rating, create a condition that matches exactly that rating
        // This fixes the previous issue where ranges were used
        $ratingConditions[] = "(rating = ?)";
        $params[] = $ratingVal;
        $paramTypes .= 'd';
    }

    if (!empty($ratingConditions)) {
        $whereClause .= " AND (" . implode(" OR ", $ratingConditions) . ")";
    }
}

// Get total number of books (with filters applied)
$totalQuery = "SELECT COUNT(*) as total FROM books" . $whereClause;
$totalStmt = $conn->prepare($totalQuery);

if (!empty($params)) {
    $totalStmt->bind_param($paramTypes, ...$params);
}

$totalStmt->execute();
$totalResult = $totalStmt->get_result();
$totalRow = $totalResult->fetch_assoc();
$totalBooks = $totalRow['total'];
$totalPages = ceil($totalBooks / $booksPerPage);

// Fetch books for current page (with filters applied)
$sql = "SELECT * FROM books" . $whereClause . " LIMIT ?, ?";
$stmt = $conn->prepare($sql);

if (!empty($params)) {
    $bindParams = $params;
    $bindParams[] = $startFrom;
    $bindParams[] = $booksPerPage;
    $stmt->bind_param($paramTypes . 'ii', ...$bindParams);
} else {
    $stmt->bind_param('ii', $startFrom, $booksPerPage);
}

$stmt->execute();
$result = $stmt->get_result();

// Store the result in an array so we can use it multiple times
$books = array();
while ($row = $result->fetch_assoc()) {
    $books[] = $row;
}

// Add this to your existing PHP code, right after you get the other parameters
// Get sorting parameter from URL
$sortOption = isset($_GET['sort']) ? $_GET['sort'] : 'default';

// Build the WHERE clause for genre, price, and rating filtering
$whereClause = ' WHERE 1=1';
$params = array();
$paramTypes = '';

// Add genre filter if selected
if (!empty($selectedGenre)) {
    $whereClause .= " AND genres = ?";
    $params[] = $selectedGenre;
    $paramTypes .= 's';
}

// Add price filter
$whereClause .= " AND price BETWEEN ? AND ?";
$params[] = $minPrice;
$params[] = $maxPrice;
$paramTypes .= 'dd'; // double for price values

// Add rating filter if selected
if (!empty($selectedRatings)) {
    $ratingConditions = [];
    foreach ($selectedRatings as $rating) {
        // Convert rating to integer to ensure proper comparison
        $ratingVal = (int)$rating;

        // For each rating, create a condition that matches exactly that rating
        $ratingConditions[] = "(rating = ?)";
        $params[] = $ratingVal;
        $paramTypes .= 'd';
    }

    if (!empty($ratingConditions)) {
        $whereClause .= " AND (" . implode(" OR ", $ratingConditions) . ")";
    }
}

// Add ORDER BY clause based on sort option
$orderByClause = "";
switch ($sortOption) {
    case 'popularity':
        // If there's no sales_count column, fall back to rating as a proxy for popularity
        $orderByClause = " ORDER BY rating DESC";
        break;
    case 'rating':
        $orderByClause = " ORDER BY rating DESC";
        break;
    case 'latest':
        // Use bookid as a proxy for "latest" assuming higher IDs are more recent books
        $orderByClause = " ORDER BY bookid DESC";
        break;
    default:
        // Use bookid for default sorting
        $orderByClause = " ORDER BY bookid ASC";
        break;
}

// Get total number of books (with filters applied)
$totalQuery = "SELECT COUNT(*) as total FROM books" . $whereClause;
$totalStmt = $conn->prepare($totalQuery);

if (!empty($params)) {
    $totalStmt->bind_param($paramTypes, ...$params);
}

$totalStmt->execute();
$totalResult = $totalStmt->get_result();
$totalRow = $totalResult->fetch_assoc();
$totalBooks = $totalRow['total'];
$totalPages = ceil($totalBooks / $booksPerPage);

// Fetch books for current page (with filters applied and sorting)
$sql = "SELECT * FROM books" . $whereClause . $orderByClause . " LIMIT ?, ?";
$stmt = $conn->prepare($sql);

if (!empty($params)) {
    $bindParams = $params;
    $bindParams[] = $startFrom;
    $bindParams[] = $booksPerPage;
    $stmt->bind_param($paramTypes . 'ii', ...$bindParams);
} else {
    $stmt->bind_param('ii', $startFrom, $booksPerPage);
}
$stmt->execute();
$result = $stmt->get_result();

// Store the result in an array so we can use it multiple times
$books = array();
while ($row = $result->fetch_assoc()) {
    $books[] = $row;
}
?>

<section class="shop-wrapper section-padding">
    <div class="container-fluid">
        <div class="shop-content">
            <div class="row">
                <div class="col-12 rounded-box">
                    <div class="shop-notices">
                        <p class="result-text">
                            Showing <?php echo $totalBooks > 0 ? $startFrom + 1 : 0; ?>-<?php echo min($startFrom + $booksPerPage, $totalBooks); ?>
                            Of <?php echo $totalBooks; ?> Results
                        </p>
                        <div class="filter-container">
                            <div class="sort-dropdown">
                                <?php
                                // Determine the text to display based on the current sort parameter
                                $sortText = "Default Sorting";
                                if (isset($_GET['sort'])) {
                                    switch ($_GET['sort']) {
                                        case 'popularity':
                                            $sortText = "Sort by popularity";
                                            break;
                                        case 'rating':
                                            $sortText = "Sort by average rating";
                                            break;
                                        case 'latest':
                                            $sortText = "Sort by latest";
                                            break;
                                    }
                                }
                                ?>
                                <span class="selected-option"><?php echo $sortText; ?> <i class="fa fa-chevron-down"></i></span>
                                <ul class="dropdown-list">
                                    <li class="dropdown-item <?php echo (!isset($_GET['sort']) || $_GET['sort'] == 'default') ? 'selected' : ''; ?>">
                                        <?php
                                        $params = $_GET;
                                        unset($params['sort']);
                                        $params['page'] = 1;
                                        $defaultUrl = '?' . http_build_query($params);
                                        ?>
                                        <a href="<?php echo htmlspecialchars($defaultUrl); ?>">Default sorting</a>
                                    </li>
                                    <li class="dropdown-item <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'popularity') ? 'selected' : ''; ?>">
                                        <?php
                                        $params = $_GET;
                                        $params['sort'] = 'popularity';
                                        $params['page'] = 1;
                                        $popularityUrl = '?' . http_build_query($params);
                                        ?>
                                        <a href="<?php echo htmlspecialchars($popularityUrl); ?>">Sort by popularity</a>
                                    </li>
                                    <li class="dropdown-item <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'rating') ? 'selected' : ''; ?>">
                                        <?php
                                        $params = $_GET;
                                        $params['sort'] = 'rating';
                                        $params['page'] = 1;
                                        $ratingUrl = '?' . http_build_query($params);
                                        ?>
                                        <a href="<?php echo htmlspecialchars($ratingUrl); ?>">Sort by average rating</a>
                                    </li>
                                    <li class="dropdown-item <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'latest') ? 'selected' : ''; ?>">
                                        <?php
                                        $params = $_GET;
                                        $params['sort'] = 'latest';
                                        $params['page'] = 1;
                                        $latestUrl = '?' . http_build_query($params);
                                        ?>
                                        <a href="<?php echo htmlspecialchars($latestUrl); ?>">Sort by latest</a>
                                    </li>
                                </ul>
                            </div>
                            <!-- <div class="view-icon">
                                <a href="/Booksy/shoplist.php"><i class="bi bi-list"></i></a>
                            </div> -->
                            <div class="grid-icon active">
                                <a href="/Booksy/shop.php"><i class="bi bi-grid"></i></a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-lg-4">
                    <div class="main-sidebar">
                        <!--  Search Section -->
                        <div class="single-sidebar-widget search-widget">
                            <div class="wid-title">Search</div>
                            <form action="#">
                                <div class="input-area search-container">
                                    <input class="search-input" type="text" placeholder="Search here">
                                    <button class="cmn-btn search-icon"><i class="bi bi-search"></i></button>
                                </div>
                            </form>
                        </div>

                        <!--  Categories -->
                        <div class="single-sidebar-widget category-widget">
                            <div class="wid-title">Categories</div>
                            <ul class="category-list">
                                <?php
                                // Display the genres with book counts
                                for ($i = 0; $i < min($displayedGenres, $totalGenres); $i++) {
                                    $genre = $allGenres[$i]['genres'];
                                    $bookCount = $allGenres[$i]['book_count'];
                                    $activeClass = ($genre === $selectedGenre) ? 'active' : '';
                                    // Create a URL that preserves existing parameters but updates the genre
                                    $genreUrl = '?' . http_build_query(array_merge($_GET, ['genre' => $genre, 'page' => 1]));
                                ?>
                                    <li>
                                        <a href="<?php echo htmlspecialchars($genreUrl); ?>">
                                            <button class="category-btn <?php echo $activeClass; ?>">
                                                <?php echo htmlspecialchars($genre); ?>
                                                <span class="book-count">(<?php echo $bookCount; ?>)</span>
                                            </button>
                                        </a>
                                    </li>
                                <?php } ?>

                                <?php if (!$showAllCategories && $totalGenres > $categoriesPerPage): ?>
                                    <li class="view-more-item">
                                        <?php
                                        // Create a URL that preserves existing parameters but adds show_all_categories=1
                                        $viewMoreUrl = '?' . http_build_query(array_merge($_GET, ['show_all_categories' => 1]));
                                        ?>
                                        <a href="<?php echo htmlspecialchars($viewMoreUrl); ?>">
                                            <button class="category-btn view-more">
                                                View More
                                                <span class="book-count">(<?php echo $totalGenres - $categoriesPerPage; ?> more)</span>
                                            </button>
                                        </a>
                                    </li>
                                <?php elseif ($showAllCategories && $totalGenres > $categoriesPerPage): ?>
                                    <li class="view-less-item">
                                        <?php
                                        // Create a URL that preserves existing parameters but removes show_all_categories
                                        $viewLessParams = $_GET;
                                        unset($viewLessParams['show_all_categories']);
                                        $viewLessUrl = '?' . http_build_query($viewLessParams);
                                        ?>
                                        <a href="<?php echo htmlspecialchars($viewLessUrl); ?>">
                                            <button class="category-btn view-less">
                                                View Less
                                            </button>
                                        </a>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </div>
                        <!--  Product Filters -->
                        <div class="single-sidebar-widget">
                            <div class="wid-title">Product Status</div>
                            <div class="product-status">
                                <!-- In Stock Dropdown -->
                                <div class="status-dropdown stock-dropdown">
                                    <span class="selected-status">In Stock</span>
                                    <i class="bi bi-chevron-down"></i>
                                    <ul class="status-list">
                                        <li data-value="1" class="status-option selected">In Stock</li>
                                        <li data-value="2" class="status-option">Castle In The Sky</li>
                                        <li data-value="3" class="status-option">The Hidden Mystery Behind</li>
                                        <li data-value="4" class="status-option">Flovely And Unicom Erna</li>
                                    </ul>
                                </div>

                                <!-- On Sale Dropdown -->
                                <div class="status-dropdown sale-dropdown">
                                    <span class="selected-status">On Sale</span>
                                    <i class="bi bi-chevron-down"></i>
                                    <ul class="status-list">
                                        <li data-value="1" class="status-option selected">On Sale</li>
                                        <li data-value="2" class="status-option">Flovely And Unicom Erna</li>
                                        <li data-value="3" class="status-option">Castle In The Sky</li>
                                        <li data-value="4" class="status-option">How Deal With Very Bad BOOK</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!--Price Filter -->
                        <div class="single-sidebar-widget price-widget">
                            <div class="wid-title">Filter By Price</div>
                            <form id="price-filter-form" method="GET" action="">
                                <!-- Preserve existing query parameters -->
                                <?php foreach ($_GET as $key => $value): ?>
                                    <?php if ($key != 'min_price' && $key != 'max_price' && $key != 'page'): ?>
                                        <?php if (is_array($value)): ?>
                                            <?php foreach ($value as $val): ?>
                                                <input type="hidden" name="<?php echo htmlspecialchars($key); ?>[]" value="<?php echo htmlspecialchars($val); ?>">
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <input type="hidden" name="<?php echo htmlspecialchars($key); ?>" value="<?php echo htmlspecialchars($value); ?>">
                                        <?php endif; ?>
                                    <?php endif; ?>
                                <?php endforeach; ?>

                                <!-- Reset page to 1 when filter changes -->
                                <input type="hidden" name="page" value="1">

                                <div class="price-slider">
                                    <input type="range" min="0" max="100" value="<?php echo $maxPrice; ?>" id="price-range"
                                        oninput="updatePriceLabel()">
                                    <input type="hidden" id="min-price" name="min_price" value="<?php echo $minPrice; ?>">
                                    <input type="hidden" id="max-price" name="max_price" value="<?php echo $maxPrice; ?>">
                                    <p id="price-label">Price: $<?php echo $minPrice; ?> - $<?php echo $maxPrice; ?></p>
                                    <button type="submit" class="filter-btn">Filter</button>
                                </div>
                            </form>

                            <script>
                                function updatePriceLabel() {
                                    const rangeValue = document.getElementById('price-range').value;
                                    document.getElementById('max-price').value = rangeValue;
                                    document.getElementById('price-label').textContent =
                                        `Price: $${document.getElementById('min-price').value} - $${rangeValue}`;
                                }
                            </script>
                        </div>

                        <!--Review -->
                        <div class="single-sidebar-widget review-widget">
                            <div class="wid-title">By Review</div>
                            <form id="rating-filter-form" method="GET" action="">
                                <!-- Preserve existing query parameters -->
                                <?php foreach ($_GET as $key => $value): ?>
                                    <?php if ($key != 'ratings' && $key != 'page'): ?>
                                        <?php if (is_array($value)): ?>
                                            <?php foreach ($value as $val): ?>
                                                <input type="hidden" name="<?php echo htmlspecialchars($key); ?>[]" value="<?php echo htmlspecialchars($val); ?>">
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <input type="hidden" name="<?php echo htmlspecialchars($key); ?>" value="<?php echo htmlspecialchars($value); ?>">
                                        <?php endif; ?>
                                    <?php endif; ?>
                                <?php endforeach; ?>

                                <!-- Reset page to 1 when filter changes -->
                                <input type="hidden" name="page" value="1">

                                <div class="review-list">
                                    <label class="review-checkbox d-flex align-items-center">
                                        <span class="d-flex gap-3 align-items-center">
                                            <span class="checkbox-area d-flex align-items-center">
                                                <input type="checkbox" id="review-5" name="ratings[]" value="5"
                                                    <?php echo in_array('5', $selectedRatings) ? 'checked' : ''; ?>
                                                    onclick="this.form.submit();">
                                                <span class="checkmark d-flex align-items-center"></span>
                                            </span>
                                            <span class="review-text">
                                                <span class="stars">
                                                    <i class="bi bi-star-fill"></i>
                                                    <i class="bi bi-star-fill"></i>
                                                    <i class="bi bi-star-fill"></i>
                                                    <i class="bi bi-star-fill"></i>
                                                    <i class="bi bi-star-fill"></i>
                                                </span>
                                                5
                                            </span>
                                        </span>
                                    </label>
                                    <label class="review-checkbox d-flex align-items-center">
                                        <span class="d-flex gap-3 align-items-center">
                                            <span class="checkbox-area d-flex align-items-center">
                                                <input type="checkbox" id="review-4" name="ratings[]" value="4"
                                                    <?php echo in_array('4', $selectedRatings) ? 'checked' : ''; ?>
                                                    onclick="this.form.submit();">
                                                <span class="checkmark d-flex align-items-center"></span>
                                            </span>
                                            <span class="review-text">
                                                <span class="stars">
                                                    <i class="bi bi-star-fill"></i>
                                                    <i class="bi bi-star-fill"></i>
                                                    <i class="bi bi-star-fill"></i>
                                                    <i class="bi bi-star-fill"></i>
                                                    <i class="bi bi-star"></i>
                                                </span>
                                                4
                                            </span>
                                        </span>
                                    </label>
                                    <label class="review-checkbox d-flex align-items-center">
                                        <span class="d-flex gap-3 align-items-center">
                                            <span class="checkbox-area d-flex align-items-center">
                                                <input type="checkbox" id="review-3" name="ratings[]" value="3"
                                                    <?php echo in_array('3', $selectedRatings) ? 'checked' : ''; ?>
                                                    onclick="this.form.submit();">
                                                <span class="checkmark d-flex align-items-center"></span>
                                            </span>
                                            <span class="review-text">
                                                <span class="stars">
                                                    <i class="bi bi-star-fill"></i>
                                                    <i class="bi bi-star-fill"></i>
                                                    <i class="bi bi-star-fill"></i>
                                                    <i class="bi bi-star"></i>
                                                    <i class="bi bi-star"></i>
                                                </span>
                                                3
                                            </span>
                                        </span>
                                    </label>
                                    <label class="review-checkbox d-flex align-items-center">
                                        <span class="d-flex gap-3 align-items-center">
                                            <span class="checkbox-area d-flex align-items-center">
                                                <input type="checkbox" id="review-2" name="ratings[]" value="2"
                                                    <?php echo in_array('2', $selectedRatings) ? 'checked' : ''; ?>
                                                    onclick="this.form.submit();">
                                                <span class="checkmark d-flex align-items-center"></span>
                                            </span>
                                            <span class="review-text">
                                                <span class="stars">
                                                    <i class="bi bi-star-fill"></i>
                                                    <i class="bi bi-star-fill"></i>
                                                    <i class="bi bi-star"></i>
                                                    <i class="bi bi-star"></i>
                                                    <i class="bi bi-star"></i>
                                                </span>
                                                2
                                            </span>
                                        </span>
                                    </label>
                                    <label class="review-checkbox d-flex align-items-center">
                                        <span class="d-flex gap-3 align-items-center">
                                            <span class="checkbox-area d-flex align-items-center">
                                                <input type="checkbox" id="review-1" name="ratings[]" value="1"
                                                    <?php echo in_array('1', $selectedRatings) ? 'checked' : ''; ?>
                                                    onclick="this.form.submit();">
                                                <span class="checkmark d-flex align-items-center"></span>
                                            </span>
                                            <span class="review-text">
                                                <span class="stars">
                                                    <i class="bi bi-star-fill"></i>
                                                    <i class="bi bi-star"></i>
                                                    <i class="bi bi-star"></i>
                                                    <i class="bi bi-star"></i>
                                                    <i class="bi bi-star"></i>
                                                </span>
                                                1
                                            </span>
                                        </span>
                                    </label>
                                </div>
                            </form>
                        </div>

                    </div>
                </div>

                <!-- Book Section -->
                <div class="col-lg-9 col-md-8">
                    <div class="row">
                        <?php if (count($books) > 0): ?>
                            <?php foreach ($books as $row): ?>
                                <div class="col-xl-3 col-lg-4 col-md-6 col-sm-12 book-column">
                                    <div class="book-card">
                                        <div class="book-thumb">
                                            <a href="shopdetail.php?isbn=<?php echo $row['isbn']; ?>">
                                                <img src="<?php echo $row['coverimg']; ?>" alt="<?php echo $row['title']; ?>">
                                            </a>
                                            <ul class="book-icons d-grid justify-content-center align-items-center">
                                                <li><a href="shop-cart.html"><i class="bi bi-heart"></i></a></li>
                                                <li><a href="shop-cart.html"><i class="bi bi-arrow-left-right"></i></a></li>
                                                <li><a href="shopdetail.php?isbn=<?php echo $row['isbn']; ?>"><i class="bi bi-eye"></i></a></li>
                                            </ul>

                                        </div>
                                        <div class="book-content">
                                            <h3><a href="shopdetail.php?isbn=<?php echo $row['isbn']; ?>"><?php echo $row['title']; ?></a></h3>
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
                            <div class="col-12 text-center my-5">
                                <p>No books found matching your criteria. Try adjusting your filters.</p>
                            </div>
                        <?php endif; ?>
                    </div>

                    <?php if ($totalPages > 1): ?>
                        <div class="pagination-container text-center mt-5">
                            <ul class="pagination">
                                <!-- Previous button -->
                                <?php
                                // Create a URL that preserves existing parameters but updates the page
                                $prevPageUrl = '?' . http_build_query(array_merge($_GET, ['page' => max(1, $page - 1)]));
                                $nextPageUrl = '?' . http_build_query(array_merge($_GET, ['page' => min($totalPages, $page + 1)]));
                                ?>
                                <li class="page-item <?php echo ($page <= 1) ? 'disabled' : ''; ?>">
                                    <a class="page-link" href="<?php echo ($page <= 1) ? '#' : htmlspecialchars($prevPageUrl); ?>" <?php echo ($page <= 1) ? 'aria-disabled="true"' : ''; ?>>
                                        Previous
                                    </a>
                                </li>

                                <?php
                                $visiblePages = array();

                                if ($page > 1) {
                                    $visiblePages[] = $page - 1;
                                }

                                $visiblePages[] = $page;

                                if ($page < $totalPages) {
                                    $visiblePages[] = $page + 1;
                                }

                                // Display the pages
                                foreach ($visiblePages as $i):
                                    $pageUrl = '?' . http_build_query(array_merge($_GET, ['page' => $i]));
                                ?>
                                    <li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>">
                                        <a class="page-link" href="<?php echo htmlspecialchars($pageUrl); ?>"><?php echo $i; ?></a>
                                    </li>
                                <?php endforeach; ?>

                                <!-- Dots box for remaining pages if needed -->
                                <?php if ($page < $totalPages - 1): ?>
                                    <li class="page-item disabled">
                                        <span class="page-link">...</span>
                                    </li>
                                <?php endif; ?>

                                <!-- Next button -->
                                <li class="page-item <?php echo ($page >= $totalPages) ? 'disabled' : ''; ?>">
                                    <a class="page-link" href="<?php echo ($page >= $totalPages) ? '#' : htmlspecialchars($nextPageUrl); ?>" <?php echo ($page >= $totalPages) ? 'aria-disabled="true"' : ''; ?>>
                                        Next
                                    </a>
                                </li>
                            </ul>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Get the dropdown elements
        const sortDropdown = document.querySelector('.sort-dropdown');
        if (sortDropdown) {
            const selectedOption = sortDropdown.querySelector('.selected-option');
            const dropdownList = sortDropdown.querySelector('.dropdown-list');

            // Toggle dropdown list on click
            selectedOption.addEventListener('click', function() {
                dropdownList.classList.toggle('active');
            });

            // Close dropdown when clicking outside
            document.addEventListener('click', function(event) {
                if (!sortDropdown.contains(event.target)) {
                    dropdownList.classList.remove('active');
                }
            });
        }
    });
</script>
<?php include 'Pages/footer.php'; ?>