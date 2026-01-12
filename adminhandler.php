<?php
session_start();
include "db_conn.php";

/* ==================== ADMIN LOGIN ==================== */
if (isset($_POST['action']) && $_POST['action'] === 'login') {

    $user_name = trim($_POST['user_name']);
    $password  = $_POST['password'];

    $stmt = $conn->prepare(
        "SELECT id, password, is_active FROM admin WHERE user_name = ? LIMIT 1"
    );
    $stmt->bind_param("s", $user_name);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        $_SESSION['alert'] = [
            'type' => 'error',
            'title' => 'Login Failed',
            'message' => 'Invalid username'
        ];
        header("Location: login.php");
        exit();
    }

    $admin = $result->fetch_assoc();

    if ($admin['is_active'] == 0) {
        $_SESSION['alert'] = [
            'type' => 'error',
            'title' => 'Account Disabled',
            'message' => 'Your account is inactive'
        ];
        header("Location: login.php");
        exit();
    }

    if (!password_verify($password, $admin['password'])) {
        $_SESSION['alert'] = [
            'type' => 'error',
            'title' => 'Login Failed',
            'message' => 'Incorrect password'
        ];
        header("Location: login.php");
        exit();
    }

    $_SESSION['admin_id']   = $admin['id'];
    $_SESSION['admin_name'] = $user_name;
    $_SESSION['logged_in']  = true;

    header("Location: Dashboard/manage_student.php");
    exit();
}


/* ==================== ADMIN SIGNUP ==================== */
if (isset($_POST['action']) && $_POST['action'] === 'signup') {

    $user_name = trim($_POST['user_name']);
    $password  = $_POST['password'];

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
            'message' => 'Username already exists'
        ];
        header("Location: login.php");
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
        die("Signup failed: " . $stmt->error);
    }

    $_SESSION['alert'] = [
        'type' => 'success',
        'title' => 'Signup Successful',
        'message' => 'Admin account created. You can now login.'
    ];

    header("Location: login.php");
    exit();
}

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

$conn->close();
