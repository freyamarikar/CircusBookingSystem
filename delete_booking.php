<?php
session_start();
include 'config.php';

// Use POST instead of GET
if(isset($_POST['delete_booking']) && isset($_POST['booking_id'])){
    $booking_id = intval($_POST['booking_id']);

    // Admin can delete any booking
    if(isset($_SESSION['admin_id'])){
        // Get tickets_booked and show_id before deleting
        $stmt = $conn->prepare("SELECT show_id, tickets_booked FROM bookings WHERE booking_id=?");
        $stmt->bind_param("i", $booking_id);
        $stmt->execute();
        $res = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if($res){
            // Update seats in shows table
            $stmt = $conn->prepare("UPDATE shows SET seats_available = seats_available + ? WHERE show_id=?");
            $stmt->bind_param("ii", $res['tickets_booked'], $res['show_id']);
            $stmt->execute();
            $stmt->close();

            // Delete the booking
            $stmt = $conn->prepare("DELETE FROM bookings WHERE booking_id=?");
            $stmt->bind_param("i", $booking_id);
            $stmt->execute();
            $stmt->close();

            echo "<script>alert('Booking deleted successfully!'); window.location='admin_panel.php';</script>";
            exit;
        }
    }

    // User can delete their own booking
    if(isset($_SESSION['user_id'])){
        $user_id = $_SESSION['user_id'];

        // Check if booking belongs to this user
        $stmt = $conn->prepare("SELECT show_id, tickets_booked FROM bookings WHERE booking_id=? AND user_id=?");
        $stmt->bind_param("ii", $booking_id, $user_id);
        $stmt->execute();
        $res = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if($res){
            // Update seats in shows table
            $stmt = $conn->prepare("UPDATE shows SET seats_available = seats_available + ? WHERE show_id=?");
            $stmt->bind_param("ii", $res['tickets_booked'], $res['show_id']);
            $stmt->execute();
            $stmt->close();

            // Delete the booking
            $stmt = $conn->prepare("DELETE FROM bookings WHERE booking_id=? AND user_id=?");
            $stmt->bind_param("ii", $booking_id, $user_id);
            $stmt->execute();
            $stmt->close();

            echo "<script>alert('Booking canceled successfully!'); window.location='my_bookings.php';</script>";
            exit;
        } else {
            echo "<script>alert('Invalid booking or permission denied!'); window.location='my_bookings.php';</script>";
            exit;
        }
    }
}

// If nothing matches, redirect
header("Location: index.php");
exit;
?>
