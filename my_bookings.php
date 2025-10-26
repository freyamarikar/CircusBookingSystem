<?php
session_start();
include 'config.php';

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("
    SELECT b.booking_id, s.show_name, s.show_date, s.ticket_price, b.tickets_booked, b.total_price, s.caption
    FROM bookings b
    JOIN shows s ON b.show_id = s.show_id
    WHERE b.user_id=?
    ORDER BY b.booking_date DESC
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$bookings = $stmt->get_result();
$stmt->close();
?>
<!DOCTYPE html>
<html>
<head>
    <title>My Bookings - Circus Mania</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            margin:0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: url('images/circus.jpg') no-repeat center center/cover;
            min-height:100vh;
            color: #fff;
            position: relative;
        }

        /* Keep your original header design, just fix position */
        header {
            position: fixed;
            top:0;
            left:0;
            width:94%;
            z-index:100;
        }


        /* Footer fixed */
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
            padding-top:90px; /* space for fixed header */
            padding-bottom:80px; /* space for fixed footer */
            width:90%;
            max-width:1200px;
            margin:0 auto;
        }

        .show-container { 
            display:flex; 
            flex-wrap:wrap; 
            gap:20px; 
            justify-content:center; 
        }

        .show-card {
            background: rgba(0,31,63,0.9); /* match header color */
            border-radius:15px;
            padding:25px;
            width:280px;
            box-shadow:0 12px 25px rgba(0,0,0,0.5);
            color:white;
            transition: transform 0.3s, box-shadow 0.3s;
        }
        .show-card:hover { transform: translateY(-5px); box-shadow:0 15px 35px rgba(0,0,0,0.6); }

        .show-card h3 { margin-top:0; color:#ffcc00; }
        .show-card p { margin:5px 0; color:white; }

        input.tickets-input { 
            width:60px; padding:6px; text-align:center; 
            background: #fff; 
            color:#001f3f; border:none; border-radius:6px; 
        }

        .update-btn, .save-btn, .cancel-btn { 
            margin-top:10px; padding:10px 15px; border:none; border-radius:8px; cursor:pointer; font-weight:bold; width:100%;
            transition:0.3s;
        }
        .update-btn { background:#ff9800; color:white; }
        .update-btn:hover { background:#ffc107; color:#001f3f; }
        .save-btn { background:#28a745; color:white; display:none; }
        .save-btn:hover { background:#5cd65c; color:#001f3f; }
        .cancel-btn { background:#e53935; color:white; }
        .cancel-btn:hover { background:#ff6659; color:white; }
    </style>
    <script>
        function enableEdit(id){
            const ticketsInput = document.getElementById('tickets-' + id);
            ticketsInput.removeAttribute('readonly');
            ticketsInput.focus();
            document.getElementById('save-' + id).style.display = 'inline-block';
        }
    </script>
</head>
<body>

<header>
    <div class="logo">🎪 Circus Mania</div>
    <nav>
        <a href="profile.php">My Profile</a>
        <a href="booking.php">Book Tickets</a>
        <a href="my_bookings.php" class="active">My Bookings</a>
        <a href="logout.php">Logout</a>
    </nav>
</header>

<div class="container">
    <h2 style="text-align:center; margin-bottom:20px;">My Bookings</h2>
    <?php if($bookings->num_rows == 0): ?>
        <p style="text-align:center;">You have no bookings yet.</p>
    <?php else: ?>
        <div class="show-container">
        <?php while($b = $bookings->fetch_assoc()): ?>
            <div class="show-card">
                <h3><?= htmlspecialchars($b['show_name']) ?></h3>
                <p><?= htmlspecialchars($b['caption']) ?></p>
                <p><strong>Show Date:</strong> <?= date("d M Y", strtotime($b['show_date'])) ?></p>
                <p><strong>Tickets Booked:</strong> 
                    <input type="number" id="tickets-<?= $b['booking_id'] ?>" class="tickets-input" value="<?= $b['tickets_booked'] ?>" min="1" readonly>
                </p>
                <p><strong>Price per Ticket:</strong> ₹<?= number_format($b['ticket_price'],2) ?></p>
                <p><strong>Total Paid:</strong> ₹<?= number_format($b['total_price'],2) ?></p>

                <form method="POST" action="update_booking.php" style="margin-top:10px;" id="form-<?= $b['booking_id'] ?>">
                    <input type="hidden" name="booking_id" value="<?= $b['booking_id'] ?>">
                    <input type="hidden" name="tickets" id="hidden-tickets-<?= $b['booking_id'] ?>" value="<?= $b['tickets_booked'] ?>">

                    <button type="button" class="update-btn" onclick="enableEdit('<?= $b['booking_id'] ?>'); document.getElementById('hidden-tickets-<?= $b['booking_id'] ?>').value=document.getElementById('tickets-<?= $b['booking_id'] ?>').value;">Update Tickets</button>
                    <button type="submit" name="update_booking" class="save-btn" id="save-<?= $b['booking_id'] ?>" onclick="document.getElementById('hidden-tickets-<?= $b['booking_id'] ?>').value=document.getElementById('tickets-<?= $b['booking_id'] ?>').value;">Save Changes</button>
                </form>

                <form method="POST" action="delete_booking.php">
                    <input type="hidden" name="booking_id" value="<?= $b['booking_id'] ?>">
                    <button type="submit" name="delete_booking" class="cancel-btn">Cancel Booking</button>
                </form>
            </div>
        <?php endwhile; ?>
        </div>
    <?php endif; ?>
</div>

<footer>
    &copy; 2025 Circus Mania | All Rights Reserved
</footer>

</body>
</html>
