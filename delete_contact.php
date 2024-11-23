<?php
session_start();
include('db.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

if (isset($_GET['id'])) {
    $contact_id = $_GET['id'];
    $user_id = $_SESSION['user_id'];

    $sql = "DELETE FROM contacts WHERE id = '$contact_id' AND user_id = '$user_id'";

    if ($conn->query($sql) === TRUE) {
        header('Location: home.php');
    } else {
        $_SESSION['error'] = "Error deleting contact: " . $conn->error;
    }
}
?>
