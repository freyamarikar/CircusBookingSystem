<?php
session_start();
include 'config.php';
if(!isset($_SESSION['user_id'])){ header("Location: login.php"); exit(); }

if($_SERVER['REQUEST_METHOD']=='POST'){
    $user_id = $_SESSION['user_id'];
    $show_id = $_POST['show_id'];
    $tickets = $_POST['tickets'];

    $stmt = $conn->prepare("SELECT seats_available,ticket_price FROM shows WHERE show_id=?");
    $stmt->bind_param("i",$show_id);
    $stmt->execute();
    $stmt->bind_result($seats,$price);
    $stmt->fetch();
    $stmt->close();

    if($tickets<=$seats){
        $total = $tickets*$price;
        $stmt=$conn->prepare("INSERT INTO bookings (user_id,show_id,tickets_booked,total_price) VALUES (?,?,?,?)");
        $stmt->bind_param("iiid",$user_id,$show_id,$tickets,$total);
        $stmt->execute();
        $stmt->close();

        $stmt=$conn->prepare("UPDATE shows SET seats_available = seats_available - ? WHERE show_id=?");
        $stmt->bind_param("ii",$tickets,$show_id);
        $stmt->execute();
        $stmt->close();

        echo "<script>alert('Booking Successful'); window.location='booking.php';</script>";
    } else { echo "<script>alert('Not enough seats'); window.location='booking.php';</script>"; }
}
?>
