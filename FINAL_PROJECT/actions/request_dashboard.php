<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 1) {
    header('Location: login.php');
    exit();
}

include('../db/db_connection1.php');

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Query to get all pending borrow requests
$sql = "SELECT * FROM borrow_requests WHERE status = 'pending'";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel ="stylesheet" href="../assets/css/request_dash.css?v=1.0">
    <title>Admin Dashboard</title>
</head>
<body>
    <h2>Admin Dashboard - Borrow Requests</h2>
    <table>
        <thead>
            <tr>
                <th>Book Title</th>
                <th>User</th>
                <th>Return Date</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><?php echo $row['book_id']; // Add book title from books table ?></td>
                <td><?php echo $row['user_id']; // Get username ?></td>
                <td><?php echo $row['return_date']; ?></td>
                <td><?php echo $row['status']; ?></td>
                <td>
                    <form action="request_status.php" method="POST">
                        <input type="hidden" name="request_id" value="<?php echo $row['request_id']; ?>">
                        <button type="submit" name="action" value="approve">Approve</button>
                        <button type="submit" name="action" value="reject">Reject</button>
                    </form>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
    <div class="btn">
    <a href="../view/admin_dashboard.html">Back to dashboard</a></div>
</body>
</html>
