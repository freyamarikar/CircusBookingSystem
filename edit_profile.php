<?php
session_start();
include 'config.php';

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Handle form submission
if($_SERVER['REQUEST_METHOD']=='POST'){
    $name = $_POST['name'];
    $phone = $_POST['phone'];

    $stmt = $conn->prepare("UPDATE users SET name=?, phone=? WHERE user_id=?");
    $stmt->bind_param("ssi",$name,$phone,$user_id);
    $stmt->execute();
    $stmt->close();

    echo "<script>alert('Profile Updated'); window.location='profile.php';</script>";
}

// Fetch current user info
$user = $conn->query("SELECT name, phone FROM users WHERE user_id=$user_id")->fetch_assoc();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Profile - Circus Mania</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="form-body">
<div class="form-card">
    <h2>Edit Profile</h2>
    <form method="POST">
        <div class="input-group">
            <input type="text" name="name" value="<?= $user['name'] ?>" required>
        </div>
        <div class="input-group">
            <input type="text" name="phone" value="<?= $user['phone'] ?>" required>
        </div>
        <button type="submit">Save Changes</button>
    </form>
    <a href="profile.php" style="display:block; margin-top:15px; color:#ff6600;">Back to Profile</a>
</div>
</body>
</html>
