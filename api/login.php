<?php
session_start();

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "attendance_db";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get POST data
$user = $_POST['username'];
$pass = $_POST['password'];

// Check admin credentials
$sql = "SELECT * FROM admin WHERE username='$user' AND password='$pass'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Login successful
    $_SESSION['admin'] = $user;
    header("Location: ../index.html"); // redirect to HTML dashboard
    exit();
} else {
    echo "<script>alert('Invalid username or password'); window.location='../login.html';</script>";
}

$conn->close();
?>
