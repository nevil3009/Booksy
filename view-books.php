<?php
session_start();
include 'db_connect.php';

// Admin session check
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || $_SESSION['category'] !== 'admin') {
    header("Location: login.php");
    exit();
}


// Fetch books with author name
$sql = "SELECT books.*, author.name AS author_name 
        FROM books 
        JOIN author ON books.authorid = author.authorid";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>View Books</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
  <div class="container mt-5">
    <h2 class="mb-4">📖 All Books</h2>
    <a href="admin-panel.php" class="btn btn-secondary mb-3">← Back to Admin Panel</a>
    <table class="table table-bordered table-striped">
      <thead class="table-dark">
        <tr>
          <th>ID</th>
          <th>Title</th>
          <th>Author</th>
          <th>Cover</th>
          <th>Price</th>
          <th>ISBN</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php while($row = $result->fetch_assoc()): ?>
        <tr>
          <td><?= $row['bookid'] ?></td>
          <td><?= $row['title'] ?></td>
          <td><?= $row['author_name'] ?></td>
          <td><img src="<?= $row['coverimg'] ?>" width="60" height="80"></td>
          <td>$<?= $row['price'] ?></td>
          <td><?= $row['isbn'] ?></td>
          <td>
            <a href="edit-book.php?id=<?= $row['bookid'] ?>" class="btn btn-warning btn-sm">Edit</a>
            <a href="delete-book.php?id=<?= $row['bookid'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this book?')">Delete</a>
          </td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</body>
</html>
