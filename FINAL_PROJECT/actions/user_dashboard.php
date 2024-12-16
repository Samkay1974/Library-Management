<?php
// Start session for user authentication
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../view/Login.html');  // Redirect to login if not logged in
    exit();
}

// Include database connection
include('../db/db_connection1.php');
$user_id = $_SESSION['user_id'];

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Handle search query
$search_query = "";
if (isset($_GET['search'])) {
    $search_query = $_GET['search'];
    $sql_books = "SELECT * FROM books WHERE title LIKE ?";
    $stmt_books = $conn->prepare($sql_books);
    $search_term = '%' . $search_query . '%';
    $stmt_books->bind_param("s", $search_term);
    $stmt_books->execute();
    $result_books = $stmt_books->get_result();
} else {
    // Fetch all books if no search query
    $sql_books = "SELECT * FROM books";
    $result_books = $conn->query($sql_books);
}

// Fetch user's borrow requests and their statuses
$sql_user_requests = "SELECT * FROM borrow_requests WHERE user_id = ?";
$stmt_user_requests = $conn->prepare($sql_user_requests);
$stmt_user_requests->bind_param("i", $user_id);
$stmt_user_requests->execute();
$result_user_requests = $stmt_user_requests->get_result();




// Store user borrow requests in an associative array
$user_borrow_requests = [];
while ($row = $result_user_requests->fetch_assoc()) {
    $user_borrow_requests[$row['book_id']] = $row; // Key is book_id
}

// Check approved borrowings for each book
$sql_approved_borrowings = "
    SELECT book_id, COUNT(*) AS approved_count 
    FROM borrow_requests 
    WHERE status = 'approved' 
    GROUP BY book_id";
$result_approved_borrowings = $conn->query($sql_approved_borrowings);

// Store approved borrowing counts for books
$approved_borrowings = [];
while ($row = $result_approved_borrowings->fetch_assoc()) {
    $approved_borrowings[$row['book_id']] = $row['approved_count'];
}

// Check for expired return dates and remove them from the database
$current_date = date('Y-m-d');
$sql_expired = "DELETE FROM borrow_requests WHERE return_date < ? AND status = 'approved'";
$stmt_expired = $conn->prepare($sql_expired);
$stmt_expired->bind_param("s", $current_date);
$stmt_expired->execute();

// Handle Cancel Request
if (isset($_POST['cancel_request'])) {
    $book_id_to_cancel = $_POST['book_id'];
    $sql_cancel = "DELETE FROM borrow_requests WHERE user_id = ? AND book_id = ? AND status != 'approved'";
    $stmt_cancel = $conn->prepare($sql_cancel);
    $stmt_cancel->bind_param("ii", $user_id, $book_id_to_cancel);
    $stmt_cancel->execute();

    // Redirect to refresh the page
    header('Location: user_dashboard.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/user_dash.css?v=1.0">
    <title>User Dashboard</title>
</head>
<body>
    <div class="logout-btn">
        <a href="logout.php">Log Out</a>
    </div>
    <h2>Welcome to Haven User Dashboard</h2>



    <!-- Search Form -->
     <div class="search">
    <form method="GET" action="user_dashboard.php">
        <input type="text" name="search" placeholder="Search for a book..." value="<?php echo htmlspecialchars($search_query); ?>">
        <button type="submit">Search</button>
    </form>
    </div>
    <h3>Available Books</h3>
    <table>
        <thead>
            <tr>
                <th>Title</th>
                <th>Author</th>
                <th>Year</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result_books->num_rows > 0) {
                while ($row = $result_books->fetch_assoc()) { 
                    $book_id = $row['book_id'];
                    $quantity = $row['quantity'];

                    // Calculate approved borrow count
                    $approved_count = isset($approved_borrowings[$book_id]) ? $approved_borrowings[$book_id] : 0;

                    // Determine if the borrow button should be disabled
                    $is_borrow_disabled = $approved_count >= $quantity;

                    // Check if the user has already made a request for this book
                    $user_has_requested = isset($user_borrow_requests[$book_id]);
            ?>
            <tr>
                <td><?php echo $row['title']; ?></td>
                <td><?php echo $row['author']; ?></td>
                <td><?php echo $row['Year']; ?></td>
                <td>
                    <?php if ($user_has_requested) { 
                        $request_status = $user_borrow_requests[$book_id]['status'];
                    ?>
                        <!-- If the user has made a request, show the status -->
                        <p><?php echo "Request Status: " . $request_status; ?></p>

                        <!-- Show Cancel Button if the request is not yet approved -->
                        <?php if ($request_status != 'approved') { ?>
                            <form action="" method="POST">
                                <input type="hidden" name="book_id" value="<?php echo $book_id; ?>">
                                <button type="submit" name="cancel_request">Cancel Request</button>
                            </form>
                        <?php } ?>
                    <?php } else { ?>
                        <!-- Show Borrow Button -->
                        <form action="borrow_book.php" method="POST">
                            <input type="hidden" name="book_id" value="<?php echo $book_id; ?>">
                            <label for="return_date">Return Date:</label>
                            <input type="date" name="return_date" required>
                            <button type="submit" <?php echo $is_borrow_disabled ? 'disabled' : ''; ?>>
                                <?php echo $is_borrow_disabled ? 'Not Available' : 'Request to Borrow'; ?>
                            </button>
                        </form>
                    <?php } ?>
                </td>
            </tr>
            <?php } 
            } else { ?>
                <tr>
                    <td colspan="4">No books found matching your search.</td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
    <div class="btn">
        <a href="../actions/view_status.php">View Borrowing Status</a>
    </div>
</body>
</html>
