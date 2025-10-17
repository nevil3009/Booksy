<?php
session_start();
include 'db_connect.php';

// Admin session check
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || $_SESSION['category'] !== 'admin') {
    header("Location: login.php");
    exit();
}


if (!isset($_GET['id'])) {
    echo "Book ID not specified.";
    exit;
}

$bookid = $_GET['id'];

// Fetch book and author data
$bookQuery = $conn->prepare("SELECT * FROM books WHERE bookid = ?");
$bookQuery->bind_param("i", $bookid);
$bookQuery->execute();
$bookResult = $bookQuery->get_result();
$book = $bookResult->fetch_assoc();

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

    $coverimg = $book['coverimg']; // Default to old image

    if ($_FILES['coverimg']['name']) {
        $target_dir = "uploads/books/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        // Delete old image if exists
        if (file_exists($book['coverimg'])) {
            unlink($book['coverimg']);
        }

        $coverimg = $target_dir . basename($_FILES["coverimg"]["name"]);
        move_uploaded_file($_FILES["coverimg"]["tmp_name"], $coverimg);
    }

    // Update book
    $stmt = $conn->prepare("UPDATE books SET title=?, rating=?, description=?, language=?, genres=?, pages=?, publisher=?, publishdate=?, coverimg=?, price=?, isbn=?, authorid=? WHERE bookid=?");
    $stmt->bind_param("sdsssiissdsii", $title, $rating, $description, $language, $genres, $pages, $publisher, $publishdate, $coverimg, $price, $isbn, $authorid, $bookid);
    $stmt->execute();
    $stmt->close();

    echo "<script>alert('Book updated successfully'); window.location.href='view-books.php';</script>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edit Book</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
  <div class="container mt-5">
    <h2>Edit Book</h2>
    <form method="POST" enctype="multipart/form-data">
      <div class="mb-3"><label class="form-label">Title</label><input type="text" class="form-control" name="title" value="<?= $book['title'] ?>" required></div>
      <div class="mb-3"><label class="form-label">Rating</label><input type="number" step="0.1" class="form-control" name="rating" value="<?= $book['rating'] ?>" required></div>
      <div class="mb-3"><label class="form-label">Description</label><textarea class="form-control" name="description" required><?= $book['description'] ?></textarea></div>
      <div class="mb-3"><label class="form-label">Language</label><input type="text" class="form-control" name="language" value="<?= $book['language'] ?>" required></div>
      <div class="mb-3"><label class="form-label">Genres</label><input type="text" class="form-control" name="genres" value="<?= $book['genres'] ?>" required></div>
      <div class="mb-3"><label class="form-label">Pages</label><input type="number" class="form-control" name="pages" value="<?= $book['pages'] ?>" required></div>
      <div class="mb-3"><label class="form-label">Publisher</label><input type="text" class="form-control" name="publisher" value="<?= $book['publisher'] ?>" required></div>
      <div class="mb-3"><label class="form-label">Publish Date</label><input type="date" class="form-control" name="publishdate" value="<?= $book['publishdate'] ?>" required></div>
      <div class="mb-3">
        <label class="form-label">Current Cover Image</label><br>
        <img src="<?= $book['coverimg'] ?>" width="80" height="100">
      </div>
      <div class="mb-3"><label class="form-label">Change Cover Image (optional)</label><input type="file" class="form-control" name="coverimg" accept="image/*"></div>
      <div class="mb-3"><label class="form-label">Price</label><input type="number" step="0.01" class="form-control" name="price" value="<?= $book['price'] ?>" required></div>
      <div class="mb-3"><label class="form-label">ISBN</label><input type="text" class="form-control" name="isbn" value="<?= $book['isbn'] ?>" required></div>
      <div class="mb-3">
        <label class="form-label">Author</label>
        <select name="authorid" class="form-select" required>
          <?php while($row = $authors->fetch_assoc()): ?>
            <option value="<?= $row['authorid'] ?>" <?= ($book['authorid'] == $row['authorid']) ? 'selected' : '' ?>>
              <?= $row['name'] ?>
            </option>
          <?php endwhile; ?>
        </select>
      </div>
      <button type="submit" class="btn btn-primary">Update Book</button>
    </form>
  </div>
</body>
</html>
