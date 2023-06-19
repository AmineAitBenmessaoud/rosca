<?php
session_start();
$bdd = new PDO('mysql:host=localhost;port=3307;dbname=u593112326_rosca;charset=utf8', 'root', 'root');


if (!$_SESSION['username']){
    header('Location : ../index.html');
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Mily - Discussions</title>
  <!-- CSS de Bootstrap -->
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <link rel="stylesheet" href="discussion.css">

  <!-- JavaScript/jQuery de Bootstrap (nécessite Popper.js) -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>


</head>



<body>
  <div class="subheader">
    <h1> Discussions en cours </h1>
  </div>
  <div class="container">


  <?php
  // Exécutez cette instruction après que l'utilisateur a été validé et que vous avez récupéré les informations de l'utilisateur à partir de la base de données


  $recupUser = $bdd->query('SELECT * FROM user');
  while ($user = $recupUser->fetch()) {
    if ($user['id'] !== $_SESSION['id']) {
      // Vérifier si un message existe entre les deux utilisateurs
      $checkMessage = $bdd->prepare('SELECT * FROM message WHERE (id_auteur = ? AND id_destinataire = ?) OR (id_auteur = ? AND id_destinataire = ?)');
      $checkMessage->execute(array($_SESSION['id'], $user['id'], $user['id'], $_SESSION['id']));
      $hasMessage = $checkMessage->rowCount() > 0;

      // Si un message existe, affichez le profil de l'utilisateur
      if ($hasMessage) {
        $lastMessageQuery = $bdd->prepare('SELECT * FROM message WHERE (id_auteur = ? AND id_destinataire = ?) OR (id_auteur = ? AND id_destinataire = ?) ORDER BY id DESC LIMIT 1');
        $lastMessageQuery->execute(array($_SESSION['id'], $user['id'], $user['id'], $_SESSION['id']));
        $lastMessage = $lastMessageQuery->fetch();
        $truncatedMessage = '';
        if (is_array($lastMessage)) {
            $truncatedMessage = mb_strimwidth($lastMessage['message'], 0, 30, '...');
        }
    
  ?>
      <a href="message.php?id=<?php echo $user['id']; ?>" class="list-group-item list-group-item-action">
        <div class="user-block">
        <img src="Photo1.jpg" alt="user-photo" class="user-photo" width="50" height="50">
          <div>
            <?php echo $user['username']; ?> - <small><?= $truncatedMessage; ?></small>
          </div>
        </div>
      </a>
  <?php
        }
      }
    }
    
  ?>
</div>
    </div>
  </div>
 


</body>
</html>
