<?php
session_start();
include 'config.php';

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit;
}

if(isset($_POST['update_booking'])){
    $user_id = $_SESSION['user_id'];
    $booking_id = $_POST['booking_id'];
    $new_tickets = (int)$_POST['tickets'];

    // Fetch current booking and show info
    $stmt = $conn->prepare("
        SELECT b.tickets_booked, s.seats_available, s.ticket_price, s.show_id
        FROM bookings b
        JOIN shows s ON b.show_id = s.show_id
        WHERE b.booking_id=? AND b.user_id=?
    ");
    $stmt->bind_param("ii", $booking_id, $user_id);
    $stmt->execute();
    $res = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if(!$res){
        echo "<script>alert('Booking not found!'); window.location='my_bookings.php';</script>";
        exit;
    }

    $current_tickets = $res['tickets_booked'];
    $available_seats = $res['seats_available'] + $current_tickets; // add back current tickets

    if($new_tickets > $available_seats){
        echo "<script>alert('Not enough seats available!'); window.location='my_bookings.php';</script>";
        exit;
    }

    $total_price = $new_tickets * $res['ticket_price'];

    // Update booking
    $stmt = $conn->prepare("UPDATE bookings SET tickets_booked=?, total_price=? WHERE booking_id=? AND user_id=?");
    $stmt->bind_param("idii", $new_tickets, $total_price, $booking_id, $user_id);
    $stmt->execute();
    $stmt->close();

    // Update show seats
    $stmt = $conn->prepare("UPDATE shows SET seats_available=? WHERE show_id=?");
    $new_seats = $available_seats - $new_tickets;
    $stmt->bind_param("ii", $new_seats, $res['show_id']);
    $stmt->execute();
    $stmt->close();

    echo "<script>alert('Booking updated successfully!'); window.location='my_bookings.php';</script>";
}
?>
