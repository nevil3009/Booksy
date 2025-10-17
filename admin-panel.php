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

$searchResults = [];
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['search'])) {
  $search = $conn->real_escape_string($_GET['search']);
  $result = $conn->query("SELECT * FROM books WHERE title LIKE '%$search%' OR isbn LIKE '%$search%'");
  while ($row = $result->fetch_assoc()) {
    $searchResults[] = $row;
  }
}

// Dynamic user category chart data
$userCategories = [];
$userCounts = [];
$userQuery = $conn->query("SELECT category, COUNT(*) as count FROM users GROUP BY category");
while ($row = $userQuery->fetch_assoc()) {
  $userCategories[] = ucfirst($row['category']);
  $userCounts[] = (int)$row['count'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Booksy Admin Dashboard</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
      transition: all 0.3s ease;
    }
    .sidebar h4 {
      margin-bottom: 30px;
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
    .topbar {
      position: sticky;
      top: 0;
      background: #fff;
      z-index: 1020;
      padding: 10px 30px;
      border-bottom: 1px solid #ddd;
      margin-left: 250px;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    .topbar .profile-dropdown {
      position: relative;
    }
    .topbar .dropdown-menu {
      right: 0;
      left: auto;
    }
    .content {
      margin-left: 270px;
      padding: 30px;
    }
    .card-icon {
      font-size: 24px;
      color: #4e73df;
    }
    @media (max-width: 768px) {
      .sidebar {
        left: -250px;
        position: fixed;
      }
      .sidebar.active {
        left: 0;
      }
      .content, .topbar {
        margin-left: 0;
      }
    }
  </style>
</head>
<body>

<div class="sidebar" id="sidebar">
  <h4>📚 Booksy Admin</h4>
  <a href="#"><i class="fas fa-chart-line me-2"></i>Dashboard</a>
  <a href="add-book.php"><i class="fas fa-plus me-2"></i>Add Book</a>
  <a href="add-author.php"><i class="fas fa-user-plus me-2"></i>Add Author</a>
  <a href="view-books.php"><i class="fas fa-book-open me-2"></i>View Books</a>
  <a href="logout.php" class="text-danger"><i class="fas fa-sign-out-alt me-2"></i>Logout</a>
</div>

<div class="topbar">
  <button class="btn btn-outline-secondary d-md-none" onclick="toggleSidebar()">
    <i class="fas fa-bars"></i>
  </button>
  <form method="GET" class="d-flex" style="width: 300px;">
    <input class="form-control me-2" type="search" name="search" placeholder="Search book title or ISBN...">
    <button class="btn btn-outline-primary" type="submit">Search</button>
  </form>
  <div class="d-flex align-items-center gap-3">
    <div class="position-relative">
      <i class="fas fa-bell fa-lg"></i>
      <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">5</span>
    </div>
    <div class="dropdown profile-dropdown">
      <a class="dropdown-toggle text-decoration-none" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
        <img src="https://ui-avatars.com/api/?name=<?= urlencode($_SESSION['full_name']) ?>&background=23395B&color=fff" alt="avatar" class="rounded-circle" width="32" height="32">
      </a>
      <ul class="dropdown-menu dropdown-menu-end">
        <li><a class="dropdown-item" href="#">Profile</a></li>
        <li><a class="dropdown-item text-danger" href="logout.php">Logout</a></li>
      </ul>
    </div>
  </div>
</div>

<div class="content">
  <h2 class="mb-4">Welcome, <?php echo htmlspecialchars($_SESSION['full_name']); ?> 👋</h2>

  <?php if (!empty($searchResults)): ?>
    <div class="card mb-4">
      <div class="card-body">
        <h5>Search Results:</h5>
        <ul class="list-group">
          <?php foreach ($searchResults as $book): ?>
            <li class="list-group-item d-flex justify-content-between align-items-center">
              <div>
                <strong><?php echo htmlspecialchars($book['title']); ?></strong> — ISBN: <?php echo htmlspecialchars($book['isbn']); ?>
              </div>
              <div>
                <a href="view-books.php?searchid=<?php echo $book['bookid']; ?>" class="btn btn-sm btn-info me-2">View</a>
                <a href="edit-book.php?bookid=<?php echo $book['bookid']; ?>" class="btn btn-sm btn-warning">Edit</a>
              </div>
            </li>
          <?php endforeach; ?>
        </ul>
      </div>
    </div>
  <?php endif; ?>

  <!-- Stats Cards -->
  <div class="row mb-4">
    <div class="col-md-3">
      <div class="card shadow-sm p-3">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <h5>$25.9k</h5>
            <p class="mb-0 text-muted">Total Earnings</p>
          </div>
          <i class="fas fa-dollar-sign card-icon"></i>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card shadow-sm p-3">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <h5>463</h5>
            <p class="mb-0 text-muted">Total Books</p>
          </div>
          <i class="fas fa-book card-icon"></i>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card shadow-sm p-3">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <h5>261</h5>
            <p class="mb-0 text-muted">Authors</p>
          </div>
          <i class="fas fa-user card-icon"></i>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card shadow-sm p-3">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <h5>5k+</h5>
            <p class="mb-0 text-muted">Monthly Readers</p>
          </div>
          <i class="fas fa-users card-icon"></i>
        </div>
      </div>
    </div>
  </div>

  <!-- Charts -->
  <div class="row">
    <div class="col-md-8 mb-4">
      <div class="card shadow-sm p-4">
        <h5>📊 Book Uploads (Monthly)</h5>
        <canvas id="uploadsChart" height="120"></canvas>
      </div>
    </div>
    <div class="col-md-4 mb-4">
      <div class="card shadow-sm p-4">
        <h5>📈 Users By Category</h5>
        <canvas id="userPieChart" height="200"></canvas>
      </div>
    </div>
  </div>
</div>

<!-- Modal -->
<div class="modal fade" id="quickEditModal" tabindex="-1" aria-labelledby="quickEditModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form>
        <div class="modal-header">
          <h5 class="modal-title" id="quickEditModalLabel">Quick Edit Book</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <input type="text" class="form-control mb-3" placeholder="Book Title" required>
          <input type="number" class="form-control mb-3" placeholder="Price" required>
          <textarea class="form-control" placeholder="Description" required></textarea>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-success">Save</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
function toggleSidebar() {
  document.getElementById('sidebar').classList.toggle('active');
}

new Chart(document.getElementById('uploadsChart'), {
  type: 'bar',
  data: {
    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
    datasets: [{
      label: 'Books Uploaded',
      data: [12, 19, 10, 17, 22, 30],
      backgroundColor: '#4e73df'
    }]
  },
  options: {
    responsive: true,
    plugins: {
      legend: { display: false }
    }
  }
});

new Chart(document.getElementById('userPieChart'), {
  type: 'doughnut',
  data: {
    labels: <?= json_encode($userCategories); ?>,
    datasets: [{
      data: <?= json_encode($userCounts); ?>,
      backgroundColor: ['#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', '#4e73df']
    }]
  },
  options: {
    responsive: true,
    plugins: {
      legend: { position: 'bottom' },
      title: {
        display: true,
        text: 'Live User Distribution from Booksy'
      }
    }
  }
});
</script>
</body>
</html>