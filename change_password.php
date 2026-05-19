<?php
/**
 * ILLUME — Change Password Handler
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Database Connection
    $host = 'localhost';
    $user = 'root';
    $pass = '';
    $db   = 'illume_db';

    $conn = new mysqli($host, $user, $pass, $db);

    if ($conn->connect_error) {
        echo json_encode(['success' => false, 'message' => 'Database connection failed.']);
        exit;
    }

    $admin_id = intval($_SESSION['admin_id']);
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
        echo json_encode(['success' => false, 'message' => 'All fields are required.']);
        exit;
    }

    if ($new_password !== $confirm_password) {
        echo json_encode(['success' => false, 'message' => 'New passwords do not match.']);
        exit;
    }

    if (strlen($new_password) < 8) {
        echo json_encode(['success' => false, 'message' => 'New password must be at least 8 characters long.']);
        exit;
    }

    // Verify current password
    $res = $conn->query("SELECT password_hash FROM admin_users WHERE id = $admin_id");
    if ($res && $res->num_rows > 0) {
        $user_data = $res->fetch_assoc();
        
        // Match default 'admin123' OR actual hash
        if ($current_password === 'admin123' || password_verify($current_password, $user_data['password_hash'])) {
            // Hash new password
            $new_hash = password_hash($new_password, PASSWORD_DEFAULT);
            $update = $conn->query("UPDATE admin_users SET password_hash = '$new_hash', updated_at = NOW() WHERE id = $admin_id");
            
            if ($update) {
                echo json_encode(['success' => true, 'message' => 'Password changed successfully.']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to update password. Please try again.']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Incorrect current password.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'User not found.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
