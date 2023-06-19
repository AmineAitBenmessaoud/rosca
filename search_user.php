<?php
$bdd = new PDO('mysql:host=localhost;port=3307;dbname=u593112326_rosca;charset=utf8', 'root', 'root');

$search = $_GET['search'] ?? '';

if ($search) {
    $searchResults = $bdd->prepare("SELECT * FROM user WHERE username LIKE ? ORDER BY username");
    $searchResults->execute(array('%' . $search . '%'));

    while ($user = $searchResults->fetch()) {
        echo '<a href="message.php?id=' . $user['id'] . '" class="list-group-item list-group-item-action">';
        echo $user['username'];
        echo '</a>';
    }
}
?>