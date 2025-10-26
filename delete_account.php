<?php
session_start();
include 'config.php';

// Check if user or admin is logged in
if(!isset($_SESSION['user_id']) && !isset($_SESSION['admin_id'])){
    header("Location: login.php");
    exit;
}

// Determine which user to delete
if(isset($_GET['id'])){
    $user_id = intval($_GET['id']);

    // Delete user's bookings first (FK constraint)
    $stmt = $conn->prepare("DELETE FROM bookings WHERE user_id=?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->close();

    // Delete user account
    $stmt = $conn->prepare("DELETE FROM users WHERE user_id=?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->close();
}

// Redirect depending on context
if(isset($_GET['from']) && $_GET['from'] == 'admin'){
    header("Location: admin_panel.php");
} else {
    // User deleted their own account, logout
    session_destroy();
    echo "<script>alert('Account deleted successfully!'); window.location='index.php';</script>";
}
exit;
?>
