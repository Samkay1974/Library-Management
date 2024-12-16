<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 1) {
    header('Location:../view/Login.html');
    exit();
}

include('../db/db_connection1.php');


ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$request_id = $_POST['request_id'];
$action = $_POST['action'];
$status = ($action == 'approve') ? 'approved' : 'rejected';

// Update the borrow request status
$sql = "UPDATE borrow_requests SET status = ? WHERE request_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("si", $status, $request_id);
$stmt->execute();

header('Location: request_dashboard.php');


if ($status == 'approved') {
    // Fetch the relevant data for the approved request
    $fetch_request_sql = "SELECT user_id, book_id, return_date FROM borrow_requests WHERE request_id = ?";
    $stmt_fetch = $conn->prepare($fetch_request_sql);
    $stmt_fetch->bind_param("i", $request_id);
    $stmt_fetch->execute();
    $result = $stmt_fetch->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $user_id = $row['user_id'];
        $book_id = $row['book_id'];
        $return_date = $row['return_date'];

        // Insert into borrowed_books table
        $insert_borrowed_sql = "
            INSERT INTO borrowed_books (user_id, book_id, borrow_date, return_date)
            VALUES (?, ?, NOW(), ?)";
        $stmt_insert = $conn->prepare($insert_borrowed_sql);
        $stmt_insert->bind_param("iis", $user_id, $book_id, $return_date);
        $stmt_insert->execute();
    }
}
?>
