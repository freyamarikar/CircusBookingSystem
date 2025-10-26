<?php
session_start();
include 'config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Circus Mania</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header style="background:#0a0a1a;">
    <div class="logo">🎪 Circus Mania</div>
    <nav>
        <a href="index.php" class="active">Home</a>
        <?php if(isset($_SESSION['user_id'])): ?>
            <a href="booking.php">Book Tickets</a>
            <a href="profile.php">My Profile</a>
            <a href="logout.php">Logout</a>
        <?php else: ?>
            <a href="register.php">Register</a>
            <a href="login.php">Login</a>
            <a href="admin_login.php">Admin Login</a>
        <?php endif; ?>
    </nav>
</header>

<!-- Hero Section -->
<section class="hero" style="background-image:url('images/circus.jpg');">
    <div class="hero-overlay"></div>
    <div class="hero-content">
        <h1>Welcome to <span>Circus Mania!</span></h1>
        <p class="hero-text">Enjoy thrilling circus shows, magical acts, and amazing animal performances.</p>
        <a href="<?php echo isset($_SESSION['user_id']) ? 'booking.php' : 'login.php'; ?>" class="hero-btn">Book Now</a>
    </div>
</section>

<!-- Featured Shows Section -->
<section class="shows" style="background:#0a0a1a; padding:50px 20px; margin:0;">
    <h2 style="color:#ffcc00; text-align:center; margin-bottom:40px;">Featured Shows</h2>
    <div class="show-container">
        <?php
        $shows = $conn->query("SELECT * FROM shows");
        while($row = $shows->fetch_assoc()):
        ?>
        <div class="show-card" style="background:#001f3f; color:white;">
            <img src="images/<?= htmlspecialchars($row['image']) ?>" alt="<?= htmlspecialchars($row['show_name']) ?>">
            <h3><?= htmlspecialchars($row['show_name']) ?></h3>
            <p><?= htmlspecialchars($row['caption']) ?></p>
        </div>
        <?php endwhile; ?>
    </div>
</section>

<!-- Customer Reviews -->
<section class="customer-review" style="background:#0a0a1a; padding:50px 20px; margin:0;">
    <h2 style="color:#ffcc00; text-align:center; margin-bottom:40px;">What Our Customers Say</h2>
    <div class="review-box" style="background:#001f3f; color:white;">
        <p>"Amazing circus experience! Highly recommended."</p>
        <span>- John Doe</span>
    </div>
    <div class="review-box" style="background:#001f3f; color:white;">
        <p>"Loved the performances, fun for the whole family!"</p>
        <span>- Sarah Lee</span>
    </div>
</section>

<footer style="background:#0a0a1a; color:white; text-align:center; padding:25px; margin-top:0;">
    <p>&copy; 2025 Circus Mania | All Rights Reserved</p>
</footer>

</body>
</html>
