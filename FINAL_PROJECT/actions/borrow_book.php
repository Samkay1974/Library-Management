<?php
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
if (!isset($_SESSION['user_id'])) {
    header('Location: ../view/Login.html');
    exit();
}

include('../db/db_connection1.php');

$user_id = $_SESSION['user_id'];
$book_id = $_POST['book_id'];
$return_date = $_POST['return_date'];

// Insert the borrow request into the borrow_requests table
$sql = "INSERT INTO borrow_requests (user_id, book_id, return_date, status) VALUES (?, ?, ?, 'pending')";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iis", $user_id, $book_id, $return_date);
$stmt->execute();

echo "Your request to borrow the book is pending approval.";



?>
