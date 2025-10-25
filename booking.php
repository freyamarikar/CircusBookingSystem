<?php
session_start();
include 'config.php';

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

if(isset($_POST['book'])){
    $show_id = $_POST['show_id'];
    $tickets = $_POST['tickets'];

    $stmt = $conn->prepare("SELECT seats_available, ticket_price FROM shows WHERE show_id=?");
    $stmt->bind_param("i",$show_id);
    $stmt->execute();
    $res = $stmt->get_result()->fetch_assoc();

    if($res['seats_available'] >= $tickets){
        $total = $tickets * $res['ticket_price'];

        $stmt = $conn->prepare("INSERT INTO bookings (user_id, show_id, tickets_booked, total_price) VALUES (?,?,?,?)");
        $stmt->bind_param("iiid",$user_id,$show_id,$tickets,$total);
        $stmt->execute();

        $stmt = $conn->prepare("UPDATE shows SET seats_available = seats_available - ? WHERE show_id=?");
        $stmt->bind_param("ii",$tickets,$show_id);
        $stmt->execute();

        $success = "Booking successful!";
    } else {
        $error = "Not enough seats available!";
    }
}

$shows = $conn->query("SELECT * FROM shows");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Book Tickets - Circus Mania</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="form-body">
<div class="form-card" style="width:90%; max-width:1000px;">
    <h2>Book Your Tickets</h2>
    <?php if(isset($success)) echo "<p class='success'>$success</p>"; ?>
    <?php if(isset($error)) echo "<p class='error'>$error</p>"; ?>
    <div class="show-container">
        <?php while($row=$shows->fetch_assoc()): ?>
        <div class="show-card">
            <img src="images/<?= $row['image'] ?>" alt="<?= $row['show_name'] ?>">
            <h3><?= $row['show_name'] ?></h3>
            <p><?= $row['caption'] ?></p>
            <p>Available Seats: <?= $row['seats_available'] ?></p>
            <form method="POST">
                <input type="hidden" name="show_id" value="<?= $row['show_id'] ?>">
                <input type="number" name="tickets" min="1" max="<?= $row['seats_available'] ?>" value="1" required style="width:60px; padding:5px; margin-bottom:10px;">
                <button type="submit" name="book">Book Now</button>
            </form>
        </div>
        <?php endwhile; ?>
    </div>
</div>
</body>
</html>
