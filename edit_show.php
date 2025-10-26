<?php
session_start();
include 'config.php';

if(!isset($_SESSION['admin_id'])){
    header("Location: admin_login.php");
    exit;
}

$show_id = $_GET['id'] ?? 0;

// Fetch show
$show = $conn->query("SELECT * FROM shows WHERE show_id=$show_id")->fetch_assoc();

// Handle update
if(isset($_POST['update_show'])){
    $name = $_POST['show_name'];
    $image = $_POST['image'];
    $date = $_POST['show_date'];
    $seats = $_POST['seats_available'];
    $price = $_POST['ticket_price'];
    $caption = $_POST['caption'];

    $stmt = $conn->prepare("UPDATE shows SET show_name=?, image=?, show_date=?, seats_available=?, ticket_price=?, caption=? WHERE show_id=?");
    $stmt->bind_param("ssiiisi",$name,$image,$date,$seats,$price,$caption,$show_id);
    $stmt->execute();
    $stmt->close();
    echo "<script>alert('Show updated successfully'); window.location='admin_panel.php';</script>";
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Show - Circus Mania</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body {
            margin:0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: url('images/circus.jpg') no-repeat center center/cover;
            min-height:100vh; color:#fff;
        }
        header {
            position: fixed; top:0; left:0; width:94%; background:#001f3f; padding:15px 50px; display:flex; justify-content:space-between; align-items:center; z-index:100; box-shadow:0 4px 10px rgba(0,0,0,0.5);
        }
        header .logo { font-size:24px; font-weight:bold; }
        header nav a { color:white; text-decoration:none; margin-left:20px; font-weight:bold; transition:0.3s; }
        header nav a:hover { border-bottom:2px solid #ffcc00; }

        footer {
            position: fixed; bottom:0; left:0; width:100%; z-index:100; background:#001f3f; color:white; text-align:center; padding:15px 0; box-shadow:0 -4px 10px rgba(0,0,0,0.5);
        }

        .container {
            padding-top:80px; padding-bottom:80px; width:100%; max-width:600px; margin:0 auto;
            background: rgba(0,31,63,0.9); border-radius:15px; padding:25px;
        }

        h2 { text-align:center; color:#ffcc00; margin-bottom:20px; }
        form input, form textarea, form button { width:100%; padding:10px; margin-bottom:15px; border-radius:6px; border:none; font-size:15px; box-sizing:border-box; }
        form input, form textarea { background:#fff; color:#001f3f; }
        form textarea { resize: vertical; min-height:60px; }
        form button { background:#ff9800; color:white; cursor:pointer; font-weight:bold; transition:0.3s; }
        form button:hover { background:#ffc107; color:#001f3f; }
    </style>
</head>
<body>

<header>
    <div class="logo">🎪 Circus Mania Admin</div>
    <nav>
        <a href="admin_panel.php"><i class="fa fa-home"></i> Admin Panel</a>
        <a href="logout.php"><i class="fa fa-sign-out-alt"></i> Logout</a>
    </nav>
</header>

<div style="height:80px;"></div>

<div class="container">
    <h2>Edit Show</h2>
    <form method="POST">
        <input type="text" name="show_name" value="<?= htmlspecialchars($show['show_name']) ?>" placeholder="Show Name" required>
        <input type="text" name="image" value="<?= htmlspecialchars($show['image']) ?>" placeholder="Image Filename" required>
        <input type="date" name="show_date" value="<?= $show['show_date'] ?>" required>
        <input type="number" name="seats_available" value="<?= $show['seats_available'] ?>" placeholder="Seats Available" required>
        <input type="number" name="ticket_price" value="<?= $show['ticket_price'] ?>" placeholder="Ticket Price" required>
        <input type="text" name="caption" value="<?= htmlspecialchars($show['caption']) ?>" placeholder="Caption" required>
        <button type="submit" name="update_show"><i class="fa fa-save"></i> Update Show</button>
    </form>
</div>

<footer>
    &copy; 2025 Circus Mania | All Rights Reserved
</footer>

</body>
</html>
