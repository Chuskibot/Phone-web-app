<?php
session_start();
include('db.php');

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);
$username = $data['username'];
$email = $data['email'];
$google_id = $data['google_id'];

// Check if the Google user already exists
$sql = "SELECT * FROM users WHERE google_id = '$google_id'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // User exists, log them in
    $user = $result->fetch_assoc();
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['email'] = $user['email'];

    echo json_encode(['success' => true]);
} else {
    // User doesn't exist, register them
    $sql = "INSERT INTO users (username, email, google_id) VALUES ('$username', '$email', '$google_id')";

    if ($conn->query($sql) === TRUE) {
        $last_id = $conn->insert_id;
        $_SESSION['user_id'] = $last_id;
        $_SESSION['username'] = $username;
        $_SESSION['email'] = $email;

        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error: ' . $conn->error]);
    }
}
?>
