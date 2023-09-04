<?php
session_start();

$host = 'localhost';
$db   = 'u593112326_rosca';
$user = 'root';  // Your database username
$pass = 'root';      // Your database password
$charset = 'utf8mb4';

$pdo = new PDO("mysql:host=$host;port=3306;dbname=$db;charset=$charset", $user, $pass);

$response = [];

if (isset($_SESSION['user_id']) && isset($_GET['receiver_id'])) {
    $sender_id = $_SESSION['user_id'];
    $receiver_id = $_GET['receiver_id'];

    // Vérifiez si l'amitié existe déjà
    $stmt = $pdo->prepare("SELECT * FROM friends WHERE (user_id = ? AND friend_id = ?) OR (user_id = ? AND friend_id = ?)");
    $stmt->execute([$sender_id, $receiver_id, $receiver_id, $sender_id]);
    if($stmt->fetch()) {
        $response = ['status' => 'error', 'message' => 'Already friends'];
        echo json_encode($response);
        exit;
    }

    // Vérifiez si une demande d'ami est déjà en attente
    $stmt = $pdo->prepare("SELECT * FROM friend_requests WHERE (sender_id = ? AND receiver_id = ?) OR (sender_id = ? AND receiver_id = ?) AND status = 'PENDING'");
    $stmt->execute([$sender_id, $receiver_id, $receiver_id, $sender_id]);
    if($stmt->fetch()) {
        $response = ['status' => 'error', 'message' => 'Friend request already pending'];
        echo json_encode($response);
        exit;
    }

    // Si tout va bien, envoyez la demande d'ami
    $stmt = $pdo->prepare("INSERT INTO friend_requests (sender_id, receiver_id, status) VALUES (?, ?, 'PENDING')");
    $stmt->execute([$sender_id, $receiver_id]);

    if ($stmt->rowCount() > 0) {
        $response = ['status' => 'success', 'message' => 'Friend request sent!'];
    } else {
        $response = ['status' => 'error', 'message' => 'Failed to send friend request.'];
    }
} else {
    $response = ['status' => 'error', 'message' => 'Invalid request.'];
}

echo json_encode($response);
?>
