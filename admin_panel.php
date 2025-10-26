<?php
session_start();
include 'config.php';

if(!isset($_SESSION['admin_id'])){
    header("Location: admin_login.php");
    exit;
}

// Default active tab
$active_tab = 'shows';

// Handle adding new show
if(isset($_POST['add_show'])){
    $name = $_POST['show_name'];
    $date = $_POST['show_date'];
    $seats = $_POST['seats_available'];
    $price = $_POST['ticket_price'];
    $caption = $_POST['caption'];
    $image = $_POST['image']; // filename only

    $stmt = $conn->prepare("INSERT INTO shows (show_name, image, show_date, seats_available, ticket_price, caption) VALUES (?,?,?,?,?,?)");
    $stmt->bind_param("ssiiis", $name, $image, $date, $seats, $price, $caption);
    $stmt->execute();
    $stmt->close();
    $success = "Show added successfully!";
}

// =================== Delete User (restore seats) ===================
if(isset($_POST['delete_user_id'])){
    $uid = $_POST['delete_user_id'];

    // Get all bookings for this user to restore seats
    $res = $conn->query("SELECT show_id, tickets_booked FROM bookings WHERE user_id=$uid");
    while($row = $res->fetch_assoc()){
        $stmt = $conn->prepare("UPDATE shows SET seats_available = seats_available + ? WHERE show_id=?");
        $stmt->bind_param("ii", $row['tickets_booked'], $row['show_id']);
        $stmt->execute();
        $stmt->close();
    }

    // Delete user's bookings
    $stmt = $conn->prepare("DELETE FROM bookings WHERE user_id=?");
    $stmt->bind_param("i",$uid);
    $stmt->execute();
    $stmt->close();

    // Delete user
    $stmt = $conn->prepare("DELETE FROM users WHERE user_id=?");
    $stmt->bind_param("i",$uid);
    $stmt->execute();
    $stmt->close();

    $user_deleted = "User and their bookings deleted successfully!";
    $active_tab = 'users';
}

// =================== Delete Booking (restore seats) ===================
if(isset($_POST['delete_booking_id'])){
    $bid = $_POST['delete_booking_id'];

    // Get tickets booked and show_id
    $stmt = $conn->prepare("SELECT show_id, tickets_booked FROM bookings WHERE booking_id=?");
    $stmt->bind_param("i",$bid);
    $stmt->execute();
    $res = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if($res){
        // Update seats in show
        $stmt = $conn->prepare("UPDATE shows SET seats_available = seats_available + ? WHERE show_id=?");
        $stmt->bind_param("ii", $res['tickets_booked'], $res['show_id']);
        $stmt->execute();
        $stmt->close();

        // Delete the booking
        $stmt = $conn->prepare("DELETE FROM bookings WHERE booking_id=?");
        $stmt->bind_param("i",$bid);
        $stmt->execute();
        $stmt->close();

        $booking_deleted = "Booking deleted successfully!";
        $active_tab = 'bookings';
    }
}

// =================== Delete Show (delete bookings automatically) ===================
if(isset($_POST['delete_show_id'])){
    $show_id = $_POST['delete_show_id'];

    // Delete all bookings for this show first
    $stmt = $conn->prepare("DELETE FROM bookings WHERE show_id=?");
    $stmt->bind_param("i",$show_id);
    $stmt->execute();
    $stmt->close();

    // Delete the show
    $stmt = $conn->prepare("DELETE FROM shows WHERE show_id=?");
    $stmt->bind_param("i",$show_id);
    $stmt->execute();
    $stmt->close();

    $success = "Show deleted successfully!";
    $active_tab = 'shows';
}

// Fetch data
$shows = $conn->query("SELECT * FROM shows ORDER BY show_date DESC");
$users = $conn->query("SELECT * FROM users ORDER BY user_id DESC");
$bookings = $conn->query("
    SELECT b.booking_id, u.name AS user_name, s.show_name, b.tickets_booked, b.total_price, b.booking_date 
    FROM bookings b
    JOIN users u ON b.user_id=u.user_id
    JOIN shows s ON b.show_id=s.show_id
    ORDER BY b.booking_date DESC
");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Panel - Circus Mania</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        /* ========== STYLES REMAIN SAME AS YOUR ORIGINAL ========== */
        body { margin:0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: url('images/circus.jpg') no-repeat center center/cover; min-height:100vh; color:#fff; }
        header { position: fixed; top:0; left:0; width:94%; background:#001f3f; padding:15px 50px; display:flex; justify-content:space-between; align-items:center; z-index:100; box-shadow:0 4px 10px rgba(0,0,0,0.5); }
        header .logo { font-size:24px; font-weight:bold; }
        header nav a { color:white; text-decoration:none; margin-left:20px; font-weight:bold; transition:0.3s; }
        header nav a:hover { border-bottom:2px solid #ffcc00; }
        footer { position: fixed; bottom:0; left:0; width:100%; z-index:100; background:#001f3f; color:white; text-align:center; padding:15px 0; box-shadow:0 -4px 10px rgba(0,0,0,0.5); }
        .container { padding-top:50px; padding-bottom:80px; width:100%; max-width:1200px; margin:0 auto; }
        .tabs { display:flex; gap:10px; justify-content:center; margin-bottom:25px; }
        .tabs button { padding:10px 18px; border:none; border-radius:8px; cursor:pointer; font-weight:bold; transition:0.3s; background: #001f3f; color:#fff; }
        .tabs button.active { background: #ff9800; color:#001f3f; }
        .card { background: rgba(0,31,63,0.9); border-radius:15px; padding:25px; margin-bottom:30px; color:white; box-shadow:0 8px 25px rgba(0,0,0,0.5); }
        .card h2 { text-align:center; margin-bottom:20px; color:#ffcc00; }
        .card input, .card textarea, .card button { width:100%; padding:10px; margin-bottom:15px; border-radius:6px; border:none; font-size:15px; box-sizing:border-box; }
        .card input, .card textarea { background:#fff; color:#001f3f; }
        .card textarea { resize: vertical; min-height:60px; }
        .card button { background:#ff9800; color:white; cursor:pointer; font-weight:bold; transition:0.3s; }
        .card button:hover { background:#ffc107; color:#001f3f; }
        .show-container { display:flex; flex-wrap:wrap; gap:25px; justify-content:center; }
        .show-card { background: rgba(0,31,63,0.9); border-radius:15px; width:280px; overflow:hidden; box-shadow:0 10px 25px rgba(0,0,0,0.5); display:flex; flex-direction:column; align-items:center; transition: transform 0.3s, box-shadow 0.3s; }
        .show-card:hover { transform:translateY(-5px); box-shadow:0 15px 35px rgba(0,0,0,0.6); }
        .show-card img { width:100%; height:180px; object-fit:cover; display:block; }
        .show-card h3 { font-size:1.2rem; margin:8px 0; text-align:center; color:#ffcc00; }
        .show-card p { font-size:0.95rem; text-align:center; margin:4px 10px; }
        .actions { display:flex; gap:10px; padding:10px; width:100%; justify-content:center; }
        .admin-btn { flex:1; padding:8px 12px; border-radius:10px; border:none; font-weight:bold; cursor:pointer; font-size:14px; }
        .edit-btn { background:#28a745; color:white; }
        .delete-btn { background:red; color:white; }
        table { width:100%; border-collapse:collapse; margin-top:15px; background: rgba(0,31,63,0.9); color:white; border-radius:10px; overflow:hidden;}
        table th, table td { border:1px solid #333; padding:8px; text-align:center; font-size:14px; }
        table th { background:#001f3f; color:white; }
        @media (max-width:768px){ .show-card { width:90%; } .show-card img { height:200px; } }
    </style>
    <script>
        function showTab(tab) {
            document.querySelectorAll('.tab-content').forEach(c => c.style.display='none');
            document.getElementById(tab).style.display='block';
            document.querySelectorAll('.tabs button').forEach(b => b.classList.remove('active'));
            document.getElementById(tab+'-btn').classList.add('active');
        }
        window.onload = function() { 
            showTab('<?= $active_tab ?>'); 
        }
    </script>
</head>
<body>

<header>
    <div class="logo">🎪 Circus Mania Admin</div>
    <nav><a href="logout.php"><i class="fa fa-sign-out-alt"></i> Logout</a></nav>
</header>

<div style="height:80px;"></div>

<div class="container">

    <div class="tabs">
        <button id="shows-btn" onclick="showTab('shows')"><i class="fa fa-ticket-alt"></i> Manage Shows</button>
        <button id="users-btn" onclick="showTab('users')"><i class="fa fa-users"></i> Manage Users</button>
        <button id="bookings-btn" onclick="showTab('bookings')"><i class="fa fa-book"></i> Manage Bookings</button>
    </div>

    <!-- Shows Tab -->
    <div class="tab-content" id="shows">
        <div class="card">
            <h2>Add New Show</h2>
            <?php if(isset($success)) echo "<p style='color:lime;text-align:center;'>$success</p>"; ?>
            <form method="POST">
                <input type="text" name="show_name" placeholder="Show Name" required>
                <input type="text" name="image" placeholder="Image Filename (e.g., magic-show.jpg)" required>
                <input type="date" name="show_date" required>
                <input type="number" name="seats_available" placeholder="Seats Available" required>
                <input type="number" name="ticket_price" placeholder="Ticket Price" required>
                <textarea name="caption" placeholder="Caption" required></textarea>
                <button type="submit" name="add_show"><i class="fa fa-plus-circle"></i> Add Show</button>
            </form>
        </div>

        <div class="show-container">
        <?php while($s = $shows->fetch_assoc()): ?>
            <div class="show-card">
                <?php if(!empty($s['image']) && file_exists("images/".$s['image'])): ?>
                    <img src="images/<?= $s['image'] ?>" alt="<?= htmlspecialchars($s['show_name']) ?>">
                <?php endif; ?>
                <h3><?= htmlspecialchars($s['show_name']) ?></h3>
                <p><?= htmlspecialchars($s['caption']) ?></p>
                <div class="actions">
                    <a href="edit_show.php?id=<?= $s['show_id'] ?>"><button class="admin-btn edit-btn"><i class="fa fa-edit"></i> Edit</button></a>
                    <form method="POST" action="" onsubmit="return confirm('Delete this show?');">
                        <input type="hidden" name="delete_show_id" value="<?= $s['show_id'] ?>">
                        <button type="submit" class="admin-btn delete-btn"><i class="fa fa-trash-alt"></i> Delete</button>
                    </form>
                </div>
            </div>
        <?php endwhile; ?>
        </div>
    </div>

    <!-- Users Tab -->
    <div class="tab-content" id="users" style="display:none;">
        <div class="card">
            <h2>All Users</h2>
            <?php if(isset($user_deleted)) echo "<p style='color:lime;text-align:center;'>$user_deleted</p>"; ?>
            <table>
                <tr><th>ID</th><th>Name</th><th>Email</th><th>Phone</th><th>Action</th></tr>
                <?php while($u = $users->fetch_assoc()): ?>
                <tr>
                    <td><?= $u['user_id'] ?></td>
                    <td><?= htmlspecialchars($u['name']) ?></td>
                    <td><?= htmlspecialchars($u['email']) ?></td>
                    <td><?= htmlspecialchars($u['phone']) ?></td>
                    <td>
                        <form method="POST" action="" onsubmit="return confirm('Delete this user?');">
                            <input type="hidden" name="delete_user_id" value="<?= $u['user_id'] ?>">
                            <button type="submit" class="admin-btn delete-btn"><i class="fa fa-trash-alt"></i></button>
                        </form>
                    </td>
                </tr>
                <?php endwhile; ?>
            </table>
        </div>
    </div>

    <!-- Bookings Tab -->
    <div class="tab-content" id="bookings" style="display:none;">
        <div class="card">
            <h2>All Bookings</h2>
            <?php if(isset($booking_deleted)) echo "<p style='color:lime;text-align:center;'>$booking_deleted</p>"; ?>
            <table>
                <tr><th>ID</th><th>User</th><th>Show</th><th>Tickets</th><th>Total</th><th>Date</th><th>Action</th></tr>
                <?php while($b = $bookings->fetch_assoc()): ?>
                <tr>
                    <td><?= $b['booking_id'] ?></td>
                    <td><?= htmlspecialchars($b['user_name']) ?></td>
                    <td><?= htmlspecialchars($b['show_name']) ?></td>
                    <td><?= $b['tickets_booked'] ?></td>
                    <td>₹<?= number_format($b['total_price'],2) ?></td>
                    <td><?= date("d M Y", strtotime($b['booking_date'])) ?></td>
                    <td>
                        <form method="POST" action="" onsubmit="return confirm('Delete this booking?');">
                            <input type="hidden" name="delete_booking_id" value="<?= $b['booking_id'] ?>">
                            <button type="submit" class="admin-btn delete-btn"><i class="fa fa-trash-alt"></i></button>
                        </form>
                    </td>
                </tr>
                <?php endwhile; ?>
            </table>
        </div>
    </div>

</div>

<footer>
    &copy; 2025 Circus Mania | All Rights Reserved
</footer>

</body>
</html>
