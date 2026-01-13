<?php
session_start();
include "db_conn.php";

/* ==================== ADMIN LOGIN ==================== */
if (isset($_POST['action']) && $_POST['action'] === 'login') {

    $user_name = trim($_POST['user_name']);
    $password  = $_POST['password'];

    // Validate input
    if (empty($user_name) || empty($password)) {
        $_SESSION['alert'] = [
            'type' => 'error',
            'title' => 'Validation Error',
            'message' => 'Username and password are required'
        ];
        header("Location: login.php");
        exit();
    }

    $stmt = $conn->prepare(
        "SELECT id, user_name, password, is_active FROM admin WHERE user_name = ? LIMIT 1"
    );
    $stmt->bind_param("s", $user_name);
    $stmt->execute();
    $result = $stmt->get_result();

    // if ($result->num_rows === 0) {
    //     $_SESSION['alert'] = [
    //         'type' => 'error',
    //         'title' => 'Login Failed',
    //         'message' => 'Invalid username or password'
    //     ];
    //     $stmt->close();
    //     header("Location: login.php");
    //     exit();
    // }

    $admin = $result->fetch_assoc();
    $stmt->close();

    // // Check if account is active
    // if ($admin['is_active'] == 0) {
    //     $_SESSION['alert'] = [
    //         'type' => 'error',
    //         'title' => 'Account Disabled',
    //         'message' => 'Your account is inactive. Please contact administrator.'
    //     ];
    //     header("Location: login.php");
    //     exit();
    // }

    // Verify password
    if (!password_verify($password, $admin['password'])) {
        $_SESSION['alert'] = [
            'type' => 'error',
            'title' => 'Login Failed',
            'message' => 'Invalid username or password'
        ];
        header("Location: login.php");
        exit();
    }

        // Success alert
    $_SESSION['alert'] = [
        'type' => 'success',
        'title' => 'Login Successful',
        'message' => 'Welcome back, ' . htmlspecialchars($admin['user_name']) . '!'
    ];

    $update = $conn->prepare("UPDATE admin SET is_active = 1 WHERE id = ?");
    $update->bind_param("i", $admin['id']);
    $update->execute();
    $update->close();

    // Set session variables
    $_SESSION['admin_id']   = $admin['id'];
    $_SESSION['admin_name'] = $admin['user_name'];
    $_SESSION['logged_in']  = true;



    header("Location: Dashboard/index.php");
    exit();
}


/* ==================== ADMIN SIGNUP ==================== */
if (isset($_POST['action']) && $_POST['action'] === 'signup') {

    $user_name = trim($_POST['user_name']);
    $password  = $_POST['password'];

    // Validate input
    if (empty($user_name) || empty($password)) {
        $_SESSION['alert'] = [
            'type' => 'error',
            'title' => 'Validation Error',
            'message' => 'Username and password are required'
        ];
        header("Location: signup.php");
        exit();
    }

    // Validate password strength
    if (strlen($password) < 6) {
        $_SESSION['alert'] = [
            'type' => 'error',
            'title' => 'Weak Password',
            'message' => 'Password must be at least 6 characters long'
        ];
        header("Location: signup.php");
        exit();
    }

    // Check if username exists
    $check = $conn->prepare(
        "SELECT id FROM admin WHERE user_name = ? LIMIT 1"
    );
    $check->bind_param("s", $user_name);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        $_SESSION['alert'] = [
            'type' => 'error',
            'title' => 'Signup Failed',
            'message' => 'Username already exists. Please choose another.'
        ];
        $check->close();
        header("Location: signup.php");
        exit();
    }
    $check->close();

    // Hash password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Insert admin
    $stmt = $conn->prepare(
        "INSERT INTO admin (user_name, password, is_active)
         VALUES (?, ?, 1)"
    );
    $stmt->bind_param("ss", $user_name, $hashedPassword);

    if (!$stmt->execute()) {
        $_SESSION['alert'] = [
            'type' => 'error',
            'title' => 'Signup Failed',
            'message' => 'An error occurred. Please try again.'
        ];
        error_log("Signup error: " . $stmt->error);
        $stmt->close();
        header("Location: signup.php");
        exit();
    }

    $stmt->close();

    $_SESSION['alert'] = [
        'type' => 'success',
        'title' => 'Signup Successful',
        'message' => 'Admin account created. You can now login.'
    ];

    header("Location: login.php");
    exit();
}


/* ==================== ADMIN LOGOUT ==================== */
if ((isset($_POST['action']) && $_POST['action'] === 'logout') || 
    (isset($_GET['action']) && $_GET['action'] === 'logout')) {
    
    if (isset($_SESSION['admin_id'])) {
        $admin_id = $_SESSION['admin_id'];

        // Update is_active to 0 (mark as logged out)
        $stmt = $conn->prepare("UPDATE admin SET is_active = 0 WHERE id = ?");
        $stmt->bind_param("i", $admin_id);
        $stmt->execute();
        $stmt->close();
    }

    // Destroy session
    session_unset();
    session_destroy();

    // Set logout message
    session_start();
    $_SESSION['alert'] = [
        'type' => 'success',
        'title' => 'Logged Out',
        'message' => 'You have been successfully logged out.'
    ];

    header("Location: login.php");
    exit();
}


/* ==================== FALLBACK - REDIRECT ==================== */
// If no action is specified, redirect to login
header("Location: login.php");
exit();
?>