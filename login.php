<?php
session_start();
include 'config.php';

if(isset($_POST['login'])){
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE email=? AND password=?");
    $stmt->bind_param("ss",$email,$password);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows > 0){
        $user = $result->fetch_assoc();
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['username'] = $user['name'];
        header("Location: booking.php");
        exit;
    } else {
        $error = "Invalid email or password!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - Circus Mania</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            margin:0; padding:0;
            font-family:'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background:url('images/circus.jpg') no-repeat center center/cover;
            min-height:100vh;
            position:relative;
        }
        body::before {
            content:""; position:absolute; top:0; left:0; right:0; bottom:0;
            background: rgba(0,0,0,0.3); z-index:0;
        }
        header {
            position:relative; z-index:1;
            display:flex; justify-content:space-between; align-items:center;
            background:#0a0a1a; color:white; padding:15px 50px;
        }
        header .logo { font-size:26px; font-weight:bold; }
        header nav a { color:white; text-decoration:none; margin-left:25px; font-weight:bold; }
        header nav a:hover, header nav a.active { border-bottom:2px solid #ffcc00; }
        .form-body {
            display:flex; justify-content:center; align-items:center;
            padding:50px 0; position:relative; z-index:1;
        }
        .form-card {
            background:#0a0a1a; padding:40px 30px; border-radius:20px;
            box-shadow:0 20px 40px rgba(0,0,0,0.6); width:400px; max-width:90%;
            text-align:center; color:white;
        }
        .form-card h2 { color:#ffcc00; margin-bottom:25px; }
        .input-group input {
            width:100%; padding:15px 20px; margin-bottom:15px;
            border-radius:10px; border:1px solid #333; background:#1a1a2e; color:white;
        }
        .form-card button {
            width:100%; padding:15px;
            background:linear-gradient(45deg,#ffcc00,#ff6600); border:none; border-radius:12px;
            color:white; font-size:18px; font-weight:bold; cursor:pointer;
        }
        .form-card button:hover { background:linear-gradient(45deg,#ff6600,#ffcc00); }
        .signup-link { margin-top:15px; }
        .signup-link a { color:#ffcc00; text-decoration:none; font-weight:bold; }
        .error { color:#ff4d4d; margin-bottom:15px; }
        footer { background:#0a0a1a; color:white; text-align:center; padding:25px; margin-top:0; }
    </style>
</head>
<body>
<header>
    <div class="logo">🎪 Circus Mania</div>
    <nav>
        <a href="index.php">Home</a>
        <a href="register.php">Register</a>
        <a href="login.php" class="active">Login</a>
    </nav>
</header>

<div class="form-body">
    <div class="form-card">
        <h2>Login</h2>
        <?php if(isset($error)) echo "<p class='error'>$error</p>"; ?>
        <form method="POST">
            <div class="input-group"><input type="email" name="email" placeholder="Email" required></div>
            <div class="input-group"><input type="password" name="password" placeholder="Password" required></div>
            <button type="submit" name="login">Login</button>
            <p class="signup-link">Don't have an account? <a href="register.php">Register</a></p>
        </form>
    </div>
</div>

<footer>
    <p>&copy; 2025 Circus Mania | All Rights Reserved</p>
</footer>
</body>
</html>
