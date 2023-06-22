<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include('connect.php');
$getid = $_GET['id'];
$recupUser = $bdd->prepare('SELECT * FROM user WHERE id = ?');
$recupUser->execute(array($getid));
$destinataire = $recupUser->fetch();

$recupMessages = $bdd->prepare('SELECT message.*, fichiers.chemin FROM message LEFT JOIN fichiers ON message.id_fichier = fichiers.id WHERE (id_auteur = ? AND id_destinataire = ?) OR (id_auteur = ? AND id_destinataire = ?)');
$recupMessages->execute(array($_SESSION['id'], $getid, $getid, $_SESSION['id']));

while ($message = $recupMessages->fetch()) {
    $author = $message['id_auteur'] == $_SESSION['id'] ? $_SESSION['username'] : $destinataire['username'];
    $initial = strtoupper(substr($author, 0, 1));
    if ($message['id_destinataire'] == $_SESSION['id']) {
?>

        <p class="message message-incoming">
            <?= $message['message']; ?>
            <?php if (!empty($message['chemin'])): ?>
              <br>
              <a href="<?= $message['chemin']; ?>" target="_blank">Voir le fichier joint</a>
            <?php endif; ?>
            <span class="circle circle-incoming"><?= $initial; ?></span>
        </p>
<?php
    } elseif ($message['id_destinataire'] == $getid) {
?>
        <p class="message message-outgoing">
            <?= $message['message']; ?>
            <?php if (!empty($message['chemin'])): ?>
              <br>
              <a href="<?= $message['chemin']; ?>" target="_blank">Voir le fichier joint</a>
            <?php endif; ?>
            <span class="circle circle-outgoing"><?= $initial; ?></span>
        </p>
<?php
    }
}
?>