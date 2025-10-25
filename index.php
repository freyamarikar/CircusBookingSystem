<?php
session_start();
include 'config.php';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Circus Mania</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header>
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
        <?php endif; ?>
    </nav>
</header>

<!-- Hero Section -->
<section class="hero" style="background-image:url('images/magic-show.jpg');">
    <div class="hero-overlay"></div>
    <div class="hero-content">
        <h1>Welcome to <span>Circus Mania!</span></h1>
        <p class="hero-text">Enjoy thrilling circus shows, magical acts, and amazing animal performances.</p>
        <a href="login.php" class="hero-btn">Book Now</a>
    </div>
</section>

<!-- Featured Shows Section -->
<section class="shows">
    <h2>Featured Shows</h2>
    <div class="show-container">
        <?php
        $shows = $conn->query("SELECT * FROM shows");
        while($row = $shows->fetch_assoc()):
        ?>
        <div class="show-card">
            <img src="images/<?= $row['image'] ?>" alt="<?= $row['show_name'] ?>">
            <h3><?= $row['show_name'] ?></h3>
            <p><?= $row['caption'] ?></p>
        </div>
        <?php endwhile; ?>
    </div>
</section>

<!-- Customer Reviews -->
<section class="customer-review">
    <h2>What Our Customers Say</h2>
    <div class="review-box"><p>"Amazing circus experience! Highly recommended."</p><span>- John Doe</span></div>
    <div class="review-box"><p>"Loved the performances, fun for the whole family!"</p><span>- Sarah Lee</span></div>
</section>

<footer>
    <p>&copy; 2025 Circus Mania | All Rights Reserved</p>
</footer>
</body>
</html>
