<?php
// db_connection.php
$host = "localhost";  // Your database host (usually localhost)
$username = "samuel.ninson";   // Your database username (default for XAMPP is "root")
$password = "Sam@Ashesi2021";       // Your database password (default for XAMPP is an empty string)
$database = "webtech_fall2024_samuel_ninson";

// Create connection
$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
// Connection successful
?>