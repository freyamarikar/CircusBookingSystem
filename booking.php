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
    <style>
        body {
            margin:0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: url('images/circus.jpg') no-repeat center center/cover;
            min-height:100vh;
            position: relative;
            color: #fff;
        }

        /* Header */
        header {
            position: fixed;
            top:0;
            left:0;
            width:94%;
            z-index:100;
        }

        /* Footer */
        footer {
            position: fixed;
            bottom:0;
            left:0;
            width:100%;
            z-index:100;
            background:#001f3f;
            color:white;
            text-align:center;
            padding:15px 0;
            box-shadow:0 -4px 10px rgba(0,0,0,0.5);
        }

        .container {
            padding-top:100px; /* space for fixed header */
            padding-bottom:80px; /* space for fixed footer */
            width:90%;
            max-width:1200px;
            margin:0 auto;
        }

        .show-container { 
            display:flex; 
            flex-wrap:wrap; 
            gap:25px; 
            justify-content:center; 
        }

        .show-card {
            background: rgba(0,31,63,0.9); /* same as my_bookings */
            border-radius:15px;
            padding:25px;
            width:280px;
            box-shadow:0 12px 25px rgba(0,0,0,0.5);
            color:white;
            transition: transform 0.3s, box-shadow 0.3s;
            text-align:center;
        }

        .show-card:hover { 
            transform: translateY(-5px); 
            box-shadow:0 15px 35px rgba(0,0,0,0.6); 
        }

        .show-card h3 { 
            margin-top:0; 
            color:#ffcc00; 
        }
        .show-card p { margin:5px 0; color:white; }

        input.tickets-input { 
            width:60px; padding:6px; text-align:center; 
            background: #fff; 
            color:#001f3f; border:none; border-radius:6px; 
        }

        button { 
            margin-top:10px; padding:10px 15px; border:none; border-radius:8px; cursor:pointer; font-weight:bold; width:100%;
            transition:0.3s;
            background:#ff9800; color:white;
        }
        button:hover { background:#ffc107; color:#001f3f; }

        .success { color:green; text-align:center; margin-bottom:15px; }
        .error { color:red; text-align:center; margin-bottom:15px; }
    </style>
</head>
<body>

<header>
    <div class="logo">🎪 Circus Mania</div>
    <nav>
        <a href="profile.php">My Profile</a>
        <a href="booking.php" class="active">Book Tickets</a>
        <a href="my_bookings.php">My Bookings</a>
        <a href="logout.php">Logout</a>
    </nav>
</header>

<div class="container">
    <?php if(isset($success)) echo "<p class='success'>$success</p>"; ?>
    <?php if(isset($error)) echo "<p class='error'>$error</p>"; ?>

    <div class="show-container">
        <?php while($row = $shows->fetch_assoc()): ?>
        <div class="show-card">
            <img src="images/<?= htmlspecialchars($row['image']) ?>" alt="<?= htmlspecialchars($row['show_name']) ?>" style="width:100%; border-radius:10px; margin-bottom:10px;">
            <h3><?= htmlspecialchars($row['show_name']) ?></h3>
            <p><?= htmlspecialchars($row['caption']) ?></p>
            <p><strong>Available Seats:</strong> <?= $row['seats_available'] ?></p>
            <form method="POST">
                <input type="hidden" name="show_id" value="<?= $row['show_id'] ?>">
                <input type="number" name="tickets" min="1" max="<?= $row['seats_available'] ?>" value="1" required class="tickets-input">
                <button type="submit" name="book">Book Now</button>
            </form>
        </div>
        <?php endwhile; ?>
    </div>
</div>

<footer>
    &copy; 2025 Circus Mania | All Rights Reserved
</footer>

</body>
</html>
