<?php
include('connect.php');

$searchValue = $_GET['username'];

$stmt = $bdd->prepare("SELECT id, username FROM user WHERE username LIKE ?");
$stmt->execute(['%' . $searchValue . '%']);

$user = $stmt->fetchAll(PDO::FETCH_ASSOC);

header('Content-Type: application/json');
echo json_encode($user);
