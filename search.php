<?php
$host = 'localhost';
$db   = 'u593112326_rosca';
$user = 'root';  // Your database username
$pass = 'root';      // Your database password
$charset = 'utf8mb4';

$pdo = new PDO("mysql:host=$host;port=3306;dbname=$db;charset=$charset", $user, $pass);

$searchValue = $_GET['username'];

$stmt = $pdo->prepare("SELECT id, username FROM user WHERE username LIKE ?");
$stmt->execute(['%' . $searchValue . '%']);

$user = $stmt->fetchAll(PDO::FETCH_ASSOC);

header('Content-Type: application/json');
echo json_encode($user);
