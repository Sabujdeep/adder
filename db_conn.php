<?php
// Database credentials
$servername = "localhost";    // usually localhost doamin
$username   = "root";         // your MySQL username
$password   = "";             // your MySQL password (if any)
$dbname     = "student_management"; // your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
// echo "Connected successfully";
?>
