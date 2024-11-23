<?php
session_start();
include('db.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $user_id = $_SESSION['user_id'];

    $sql = "INSERT INTO contacts (name, phone, user_id) VALUES ('$name', '$phone', '$user_id')";

    if ($conn->query($sql) === TRUE) {
        header('Location: home.php');
    } else {
        $_SESSION['error'] = "Error adding contact: " . $conn->error;
    }
}
?>
