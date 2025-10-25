<?php
include 'config.php';

if(isset($_POST['register'])){
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $password = $_POST['password'];

    $check = $conn->prepare("SELECT user_id FROM users WHERE email=?");
    $check->bind_param("s",$email);
    $check->execute();
    $res = $check->get_result();
    if($res->num_rows > 0){
        $error = "Email already registered!";
    } else {
        $stmt = $conn->prepare("INSERT INTO users (name,email,phone,password) VALUES (?,?,?,?)");
        $stmt->bind_param("ssss",$name,$email,$phone,$password);
        if($stmt->execute()){
            header("Location: login.php");
            exit;
        } else {
            $error = "Registration failed. Try again!";
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Register - Circus Mania</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="form-body">
<div class="form-card">
    <h2>Create Account</h2>
    <?php if(isset($error)) echo "<p class='error'>$error</p>"; ?>
    <form method="POST">
        <div class="input-group">
            <input type="text" name="name" placeholder="Full Name" required>
        </div>
        <div class="input-group">
            <input type="email" name="email" placeholder="Email" required>
        </div>
        <div class="input-group">
            <input type="text" name="phone" placeholder="Phone Number" required>
        </div>
        <div class="input-group">
            <input type="password" name="password" placeholder="Password" required>
        </div>
        <button type="submit" name="register">Sign Up</button>
        <p class="signup-link">Already have an account? <a href="login.php">Login</a></p>
    </form>
</div>
</body>
</html>
