<?php
session_start();
include 'config.php';

if(!isset($_SESSION['admin_id'])){
    header("Location: admin_login.php");
    exit;
}

$show_id = $_GET['id'] ?? 0;

if($show_id){
    // Delete the show
    $stmt = $conn->prepare("DELETE FROM shows WHERE show_id=?");
    $stmt->bind_param("i",$show_id);
    $stmt->execute();
    $stmt->close();
}

header("Location: admin_panel.php");
exit;
?>
