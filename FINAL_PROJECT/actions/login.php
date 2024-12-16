<?php
include('../db/db_connection1.php');


ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

// Create a connection
$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve and sanitize form data
    $email = $conn->real_escape_string($_POST['email']);
    $password = $conn->real_escape_string($_POST['password']);

    // Check if user exists in the database
    $sql = "SELECT * FROM users WHERE email = '$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // User found, validate the password
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            // Successful login - Redirect to user management page
            $_SESSION['user_id'] = $user['user_id']; // Save user ID in session
            $_SESSION['role'] = $user['role'];       // Save user role in session
            if ($user['role'] == "1") {
                // Super Admin
                header("Location: ../view/admin_dashboard.html");
            } elseif ($user['role'] == "2") {
                // Regular Admin
                header("Location: ../actions/user_dashboard.php");
            }
            exit();
        } else {
            // Invalid password
            echo "Invalid email or password. Please try again.";
        }
    } else {
        // User does not exist
        echo "User not found. Please register now";
    }
}

// Close the connection
$conn->close();
?>
