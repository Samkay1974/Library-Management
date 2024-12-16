<?php
require_once '../db/db_connection1.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$action = $_POST['action'];

if ($action === 'add') {
    $title = $_POST['title'];
    $author = $_POST['author'];
    $year = $_POST['Year'];
    $quantity = $_POST['quantity'];

    $sql = "INSERT INTO books (title, author, Year, quantity) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssii", $title, $author, $year, $quantity);

    if ($stmt->execute()) {
        header("Location: ../view/admin_dashboard.html"); // Redirect back to the dashboard
        exit;
    } else {
        echo "Error: " . $stmt->error;
    }
} elseif ($action === 'remove') {
    $book_id = $_POST['book_id'];

    $sql = "DELETE FROM books WHERE book_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $book_id);

    if ($stmt->execute()) {
        header("Location: ../view/admin_dashboard.html"); // Redirect back to the dashboard
        exit;
    } else {
        echo "Error: " . $stmt->error;
    }
} else {
    echo "Invalid action.";
}
