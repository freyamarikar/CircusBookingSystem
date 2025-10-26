<?php
session_start();
include 'config.php';

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Securely fetch user info
$stmt = $conn->prepare("SELECT name, email, phone, password FROM users WHERE user_id=?");
$stmt->bind_param("i",$user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Handle profile update securely
if(isset($_POST['update_profile'])){
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];

    $stmt = $conn->prepare("UPDATE users SET name=?, phone=?, email=? WHERE user_id=?");
    $stmt->bind_param("sssi",$name,$phone,$email,$user_id);
    $stmt->execute();
    $stmt->close();

    $success = "Profile updated successfully!";

    // Refresh user info
    $stmt = $conn->prepare("SELECT name, email, phone, password FROM users WHERE user_id=?");
    $stmt->bind_param("i",$user_id);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

// Handle password change securely
if(isset($_POST['change_password'])){
    $current = $_POST['current_password'];
    $new = $_POST['new_password'];

    if(password_verify($current, $user['password'])){
        $hashed = password_hash($new, PASSWORD_BCRYPT);
        $stmt = $conn->prepare("UPDATE users SET password=? WHERE user_id=?");
        $stmt->bind_param("si",$hashed,$user_id);
        $stmt->execute();
        $stmt->close();
        $pass_success = "Password changed successfully!";
    } else {
        $pass_error = "Current password is incorrect!";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>My Profile - Circus Mania</title>
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

 header {
            position: fixed;
            top:0;
            left:0;
            width:94%;
            z-index:100;
        }


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
    width:100%; /* full width */
    display:flex;
    justify-content:center; /* center the profile card */
}

.show-card {
    background: rgba(0,31,63,0.9); /* semi-transparent like my_bookings */
    border-radius:15px;
    padding:30px;
    width:500px; /* slightly wider */
    max-width:95%;
    box-shadow:0 12px 25px rgba(0,0,0,0.5);
    color:white;
    transition: transform 0.3s, box-shadow 0.3s;
}

.input-group {
    margin-bottom:15px; /* space between label/button and input */
}

input[type="text"], input[type="email"], input[type="password"] {
    width:100%;
    padding:10px;
    margin-top:5px;
    border:none;
    border-radius:6px;
    font-size:15px;
    background:#fff;
    color:#001f3f;
}

        button {
            width:100%;
            padding:10px;
            border:none;
            border-radius:8px;
            font-weight:bold;
            cursor:pointer;
            margin-top:10px;
            transition:0.3s;
        }
		.edit-btn { 
    background:#ff9800; 
    color:white; 
    margin-bottom:15px; /* space between button and first input */
    font-weight:bold; 
    cursor:pointer; 
    width:100%; 
    padding:10px; 
    border:none; 
    border-radius:8px; 
}

.edit-btn:hover { 
    background:#ffc107; 
    color:#001f3f; 
}

/* Optional: add extra space above input fields if needed */
.input-group { 
    margin-bottom:15px; /* space between inputs */
}

        
        .update-btn:hover { background:#5cd65c; color:#001f3f; }
        .delete-btn { background:red; color:white; }
        .success { color:green; text-align:center; }
        .error { color:red; text-align:center; }
        hr { border:none; border-top:1px solid #ccc; margin:20px 0; }
    </style>
    <script>
        function enableEdit(section){
            const inputs = document.querySelectorAll('#'+section+' input');
            inputs.forEach(input => input.removeAttribute('readonly'));
            document.getElementById(section+'-update').style.display='block';
        }
    </script>
</head>
<body>

<header>
    <div class="logo">🎪 Circus Mania</div>
    <nav>
        <a href="profile.php" class="active">My Profile</a>
        <a href="booking.php">Book Tickets</a>
        <a href="my_bookings.php">My Bookings</a>
        <a href="logout.php">Logout</a>
    </nav>
</header>

<div class="container">

    <div class="show-card">
        <h2 style="text-align:center; margin-bottom:20px;">My Profile</h2>
        <?php if(isset($success)) echo "<p class='success'>$success</p>"; ?>

        <!-- Profile Info -->
        <div id="profile-section">
            <button type="button" class="edit-btn" onclick="enableEdit('profile-section')">Edit Profile</button>
            <form method="POST">
                <div class="input-group">
                    <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>" readonly required>
                </div>
                <div class="input-group">
                    <input type="text" name="phone" value="<?= htmlspecialchars($user['phone']) ?>" readonly required>
                </div>
                <div class="input-group">
                    <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" readonly required>
                </div>
                <button type="submit" name="update_profile" id="profile-section-update" class="update-btn" style="display:none;">Update Profile</button>
            </form>
        </div>

        <!-- Password Change -->
        <h3 style="margin-top:30px;">Change Password</h3>
        <?php if(isset($pass_error)) echo "<p class='error'>$pass_error</p>"; ?>
        <?php if(isset($pass_success)) echo "<p class='success'>$pass_success</p>"; ?>
        <div id="password-section">
            <button type="button" class="edit-btn" onclick="enableEdit('password-section')">Edit Password</button>
            <form method="POST">
                <div class="input-group">
                    <input type="password" name="current_password" placeholder="Current Password" readonly required>
                </div>
                <div class="input-group">
                    <input type="password" name="new_password" placeholder="New Password" readonly required>
                </div>
                <button type="submit" name="change_password" id="password-section-update" class="update-btn" style="display:none;">Change Password</button>
            </form>
        </div>

        <!-- Delete Account -->
        <hr>
        <form method="POST" action="delete_account.php" 
              onsubmit="return confirm('This will permanently delete your account and all bookings. Continue?');">
            <button type="submit" class="delete-btn">Delete My Account</button>
        </form>
    </div>

</div>

<footer>
    &copy; 2025 Circus Mania | All Rights Reserved
</footer>

</body>
</html>
