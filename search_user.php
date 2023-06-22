<?php include('connect.php') ?>
<?php

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