<?php
// Include the database connection
require_once '../db/db_connection1.php';


ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Start the session and check if the user is logged in
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../view/Login.html'); // Redirect to login if not logged in
    exit;
}

$user_id = $_SESSION['user_id']; // Get the logged-in user's ID

// Query to fetch the borrowed books and their status using mysqli
$query = "
    SELECT books.title, borrow_requests.request_date, borrow_requests.status, borrow_requests.return_date
    FROM borrow_requests
    JOIN books ON borrow_requests.book_id = books.book_id
    WHERE borrow_requests.user_id = ?
";

$stmt = $conn->prepare($query);

// Bind the user_id parameter to the SQL query
$stmt->bind_param('i', $user_id); // 'i' stands for integer

// Execute the query
$stmt->execute();

// Get the result of the query
$result = $stmt->get_result();

// Fetch all the results
$borrowed_books = $result->fetch_all(MYSQLI_ASSOC);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Borrowed Books Status</title>
    <link rel="stylesheet" href="../assets/css/view_status.css?v=1.0">
</head>
<body>

    <div class="dashboard">
        <h1>My Borrowed Books</h1>
        <?php if (count($borrowed_books) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Book Title</th>
                        <th>Borrow Date</th>
                        <th>Return Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($borrowed_books as $book): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($book['title']); ?></td>
                            <td><?php echo htmlspecialchars($book['request_date']); ?></td>
                            <td><?php echo htmlspecialchars($book['return_date']); ?></td>
                        
                            <td>
                                <?php 
                                if ($book['status'] == 'pending') {
                                    echo 'Pending';
                                } elseif ($book['status'] == 'approved') {
                                    echo 'Approved';
                                } else {
                                    echo 'Rejected';
                                }
                                ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>You have not borrowed any books yet.</p>
        <?php endif; ?>
    </div>
    <div class="btn">
    <a href="user_dashboard.php">Back to User Dashboard</a></div>

</body>
</html>
