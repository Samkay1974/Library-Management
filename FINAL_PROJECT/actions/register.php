<?php
include('../db/db_connection1.php');
// Create a connection
$conn = new mysqli($host, $username, $password, $database);

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve and sanitize form data
    $fname = $conn->real_escape_string($_POST['first_name']);
    $lname = $conn->real_escape_string($_POST['last_name']);
    $email = $conn->real_escape_string($_POST['email']);
    $password = $conn->real_escape_string($_POST['password']);
    $password_confirmation = $conn->real_escape_string($_POST['confirm_password']);
    
    // Check if passwords match
    if ($password !== $password_confirmation) {
        echo "Passwords do not match!";
        exit();
    }

    // Encrypt the password (recommended for security)
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    // Set timestamps
    $date_created= date("Y-m-d H:i:s");
    

    // Set role to 2 (Regular Admin) by default
    $role = 2;

    // Insert data into the database
    $sql = "INSERT INTO users (first_name, last_name, email, password, role,date_created) 
            VALUES ('$fname', '$lname', '$email', '$hashed_password', $role, '$date_created')";

    if ($conn->query($sql) === TRUE) {
        // Redirect the user to the regular recipe page
        header("Location: ../view/Login.html");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Close the connection
$conn->close();
?>
