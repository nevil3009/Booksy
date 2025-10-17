<?php
session_start();
include 'db_connect.php';

// Admin session check
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || $_SESSION['category'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Fetch authors
$authors = $conn->query("SELECT * FROM author");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $rating = $_POST['rating'];
    $description = $_POST['description'];
    $language = $_POST['language'];
    $genres = $_POST['genres'];
    $pages = $_POST['pages'];
    $publisher = $_POST['publisher'];
    $publishdate = $_POST['publishdate'];
    $price = $_POST['price'];
    $isbn = $_POST['isbn'];
    $authorid = $_POST['authorid'];

    $coverimg = '';
    if ($_FILES['coverimg']['name']) {
        $target_dir = "uploads/books/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $coverimg = $target_dir . basename($_FILES["coverimg"]["name"]);
        move_uploaded_file($_FILES["coverimg"]["tmp_name"], $coverimg);
    }

    $stmt = $conn->prepare("INSERT INTO books (title, rating, description, language, genres, pages, publisher, publishdate, coverimg, price, isbn, authorid) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sdsssiissdsi", $title, $rating, $description, $language, $genres, $pages, $publisher, $publishdate, $coverimg, $price, $isbn, $authorid);
    $stmt->execute();
    $stmt->close();

    echo "<script>alert('Book added successfully'); window.location.href='admin-panel.php';</script>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Book - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            display: flex;
            min-height: 100vh;
            background-color: #f8f9fa;
        }
        .sidebar {
            width: 250px;
            background-color: #343a40;
            color: white;
            padding: 1rem;
        }
        .sidebar h4 {
            font-size: 1.4rem;
            margin-bottom: 2rem;
        }
        .sidebar a {
            display: block;
            color: #ccc;
            padding: 10px;
            margin-bottom: 10px;
            text-decoration: none;
            border-radius: 5px;
        }
        .sidebar a:hover,
        .sidebar a.active {
            background-color: #495057;
            color: white;
        }
        .main-content {
            flex: 1;
            padding: 2rem;
        }
        .card {
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .form-label {
            font-weight: 500;
        }
    </style>
</head>
<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <h4>📚 Booksy Admin</h4>
        <a href="admin-panel.php">📊 Dashboard</a>
        <a href="add-book.php" class="active">➕ Add Book</a>
        <a href="view-books.php">📖 View Books</a>
        <a href="add-author.php">✍️ Add Author</a>
        <a href="logout.php">🚪 Logout</a>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <h2 class="mb-4">Add New Book</h2>

        <div class="card p-4">
            <form method="POST" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-md-6 mb-3"><label class="form-label">Title</label><input type="text" class="form-control" name="title" required></div>
                    <div class="col-md-6 mb-3"><label class="form-label">Rating</label><input type="number" step="0.1" class="form-control" name="rating" required></div>
                    <div class="col-12 mb-3"><label class="form-label">Description</label><textarea class="form-control" name="description" rows="3" required></textarea></div>
                    <div class="col-md-6 mb-3"><label class="form-label">Language</label><input type="text" class="form-control" name="language" required></div>
                    <div class="col-md-6 mb-3"><label class="form-label">Genres</label><input type="text" class="form-control" name="genres" required></div>
                    <div class="col-md-4 mb-3"><label class="form-label">Pages</label><input type="number" class="form-control" name="pages" required></div>
                    <div class="col-md-4 mb-3"><label class="form-label">Publisher</label><input type="text" class="form-control" name="publisher" required></div>
                    <div class="col-md-4 mb-3"><label class="form-label">Publish Date</label><input type="date" class="form-control" name="publishdate" required></div>
                    <div class="col-md-6 mb-3"><label class="form-label">Cover Image</label><input type="file" class="form-control" name="coverimg" accept="image/*" required></div>
                    <div class="col-md-3 mb-3"><label class="form-label">Price</label><input type="number" step="0.01" class="form-control" name="price" required></div>
                    <div class="col-md-3 mb-3"><label class="form-label">ISBN</label><input type="text" class="form-control" name="isbn" required></div>
                    <div class="col-md-6 mb-4">
                        <label class="form-label">Author</label>
                        <select name="authorid" class="form-select" required>
                            <option value="">-- Select Author --</option>
                            <?php while ($row = $authors->fetch_assoc()): ?>
                                <option value="<?= $row['authorid'] ?>"><?= $row['name'] ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                </div>
                <button type="submit" class="btn btn-success px-4">Add Book</button>
            </form>
        </div>
    </div>

</body>
</html>
