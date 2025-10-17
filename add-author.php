<?php
session_start();

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || $_SESSION['category'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$dbHost = "localhost";
$dbName = "booksy";
$dbUser = "root";
$dbPass = "";

$conn = new mysqli($dbHost, $dbUser, $dbPass, $dbName);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $authorName = trim($_POST['name']);

    if (!empty($authorName)) {
        $stmt = $conn->prepare("INSERT INTO author (name) VALUES (?)");
        $stmt->bind_param("s", $authorName);

        if ($stmt->execute()) {
            $_SESSION['success_message'] = "✅ Author added successfully!";
        } else {
            $_SESSION['error_message'] = "❌ Error: " . $conn->error;
        }

        $stmt->close();
        header("Location: add-author.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Add Author - Booksy Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #f9f9f9;
      font-family: 'Segoe UI', sans-serif;
    }
    .sidebar {
      height: 100vh;
      background-color: #23395B;
      padding: 20px;
      color: white;
      position: fixed;
      width: 250px;
    }
    .sidebar a {
      color: #fff;
      text-decoration: none;
      display: block;
      padding: 12px;
      margin-bottom: 10px;
      border-radius: 8px;
    }
    .sidebar a:hover {
      background-color: #1c2e48;
    }
    .content {
      margin-left: 270px;
      padding: 40px;
    }
  </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
  <h4>📚 Booksy Admin</h4>
  <a href="admin-panel.php"><i class="fas fa-chart-line me-2"></i>Dashboard</a>
  <a href="add-book.php"><i class="fas fa-plus me-2"></i>Add Book</a>
  <a href="add-author.php"><i class="fas fa-user-plus me-2"></i>Add Author</a>
  <a href="view-books.php"><i class="fas fa-book-open me-2"></i>View Books</a>
  <a href="logout.php" class="text-danger"><i class="fas fa-sign-out-alt me-2"></i>Logout</a>
</div>

<!-- Content -->
<div class="content">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Add New Author</h2>
    <a href="admin-panel.php" class="btn btn-outline-secondary">
      <i class="fas fa-arrow-left"></i> Back to Dashboard
    </a>
  </div>

  <?php if (isset($_SESSION['success_message'])): ?>
    <div class="alert alert-success">
      <?php 
        echo $_SESSION['success_message'];
        unset($_SESSION['success_message']);
      ?>
    </div>
  <?php endif; ?>

  <?php if (isset($_SESSION['error_message'])): ?>
    <div class="alert alert-danger">
      <?php 
        echo $_SESSION['error_message'];
        unset($_SESSION['error_message']);
      ?>
    </div>
  <?php endif; ?>

  <div class="card shadow-sm">
    <div class="card-body">
      <form method="POST" novalidate>
        <div class="mb-3">
          <label for="name" class="form-label">Author Name</label>
          <input type="text" class="form-control" id="name" name="name" placeholder="e.g., John Green" required>
        </div>
        <button type="submit" class="btn btn-primary">
          <i class="fas fa-user-plus me-1"></i> Add Author
        </button>
      </form>
    </div>
  </div>
</div>

</body>
</html>
