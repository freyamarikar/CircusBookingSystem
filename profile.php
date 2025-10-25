<?php
session_start();
include 'config.php';

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$user = $conn->query("SELECT * FROM users WHERE user_id=$user_id")->fetch_assoc();
$bookings = $conn->query("
    SELECT b.*, s.show_name, s.caption, s.show_date, s.ticket_price
    FROM bookings b
    JOIN shows s ON b.show_id=s.show_id
    WHERE b.user_id=$user_id
");
?>
<!DOCTYPE html>
<html>
<head>
    <title>My Profile - Circus Mania</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="form-body">
    <div class="form-card" style="width:90%; max-width:900px;">
        <h2>My Profile</h2>
        <p><strong>Name:</strong> <?= $user['name'] ?></p>
        <p><strong>Email:</strong> <?= $user['email'] ?></p>
        <p><strong>Phone:</strong> <?= $user['phone'] ?></p>
        <a href="edit_profile.php" style="display:inline-block; margin-bottom:20px; color:#ff6600;">Edit Profile ✎</a>

        <h3>My Bookings</h3>
        <?php if($bookings->num_rows==0) echo "<p>No bookings yet.</p>"; ?>
        <div class="show-container">
        <?php while($b=$bookings->fetch_assoc()): ?>
            <div class="show-card">
                <h3><?= $b['show_name'] ?></h3>
                <p><?= $b['caption'] ?></p>
                <p><strong>Date:</strong> <?= date('d M Y', strtotime($b['show_date'])) ?></p>
                <p><strong>Seats Booked:</strong> <?= $b['tickets_booked'] ?></p>
                <p><strong>Total Price:</strong> ₹<?= number_format($b['total_price'],2) ?></p>
            </div>
        <?php endwhile; ?>
        </div>
    </div>
</body>
</html>
