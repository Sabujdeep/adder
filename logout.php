<?php
session_start();

/* Unset all session variables */
$_SESSION = [];

/* Destroy the session */
session_destroy();

/* Optional: destroy session cookie (extra safety) */
if (isset($_SESSION['admin_id'])) {
    $admin_id = $_SESSION['admin_id'];

    // Update is_active to 0
    $stmt = $conn->prepare("UPDATE admin SET is_active = 0 WHERE id = ?");
    $stmt->bind_param("i", $admin_id);
    $stmt->execute();

    // Destroy session
    session_unset();
    session_destroy();
}

// Redirect to login page
header("Location: login.php");
exit();
