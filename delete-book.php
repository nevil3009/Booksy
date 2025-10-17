<?php
session_start();
include 'db_connect.php';

// Admin session check
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || $_SESSION['category'] !== 'admin') {
    header("Location: login.php");
    exit();
}

if (isset($_GET['id'])) {
    $bookid = $_GET['id'];

    // Optional: delete image file too (if you want)
    $getImg = $conn->query("SELECT coverimg FROM books WHERE bookid = $bookid");
    $imgRow = $getImg->fetch_assoc();
    if (file_exists($imgRow['coverimg'])) {
        unlink($imgRow['coverimg']);
    }

    $conn->query("DELETE FROM books WHERE bookid = $bookid");
    header("Location: view-books.php");
    exit;
}
?>
