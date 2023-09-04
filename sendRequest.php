<?php
session_start();

$host = 'localhost';
$db   = 'u593112326_rosca';
$user = 'root';  // Your database username
$pass = 'root';      // Your database password
$charset = 'utf8mb4';

$pdo = new PDO("mysql:host=$host;port=3306;dbname=$db;charset=$charset", $user, $pass);

if (isset($_SESSION['user_id']) && isset($_GET['receiver_id'])) {
    $sender_id = $_SESSION['user_id'];
    $receiver_id = $_GET['receiver_id'];

    $stmt = $pdo->prepare("INSERT INTO friend_requests (sender_id, receiver_id, status) VALUES (?, ?, 'PENDING')");
    $stmt->execute([$sender_id, $receiver_id]);

    if ($stmt->rowCount() > 0) {
        echo "Success";
    } else {
        echo "Failure";
    }
} else {
    echo "Invalid request.";
}
?>
