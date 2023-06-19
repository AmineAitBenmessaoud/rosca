<?php
session_start();
$error = "";
$bdd = new PDO('mysql:host=localhost;port=3307;dbname=u593112326_rosca;charset=utf8', 'root', 'root');

$lastFileId = null;
$destinataire = array();
$getid = $_GET['id'] ?? $_POST['id'] ?? null;

if (isset($_FILES['file']) && $_FILES['file']['error'] == 0) {
      

  // Vérifiez la taille du fichier
  $maxSize = 5 * 1024 * 1024; // Limite de taille de fichier de 5 Mo
  if ($_FILES['file']['size'] <= $maxSize) {

    // Vérifiez le type de fichier
    $allowedExtensions = array('pdf', 'doc', 'docx');
    if (isset($_FILES['file']['name'])) {
      $fileInfo = pathinfo($_FILES['file']['name']);
    } else {
      $fileInfo = array('extension' => null);
    }
  
    $fileExtension = isset($fileInfo['extension']) ? strtolower($fileInfo['extension']) : null;

    if (in_array($fileExtension, $allowedExtensions)) {
      // Déplacez le fichier téléchargé vers un répertoire spécifique
      $uploadDir = 'uploads/';
      $newFilename = uniqid() . '.' . $fileExtension;
      $uploadFile = $uploadDir . $newFilename;    

      if (move_uploaded_file($_FILES['file']['tmp_name'], $uploadFile)) {
        // Le fichier a été correctement téléchargé et déplacé
        // Vous pouvez maintenant stocker le chemin du fichier et d'autres informations dans la base de données
        include_once("../bd_connect.php");
        $query = "INSERT INTO fichiers (chemin, id_auteur) VALUES ('".$uploadFile."',".$_SESSION['id'].")";
        $error = $query;
        mysqli_query($conn, $query);
        $query = "SELECT id FROM fichiers WHERE `chemin` = '".$uploadFile."'";
        $result = mysqli_query($conn, $query);
        $row = mysqli_fetch_assoc($result);
        $lastFileId = intval($row['id']);


      } else {
        $error = "Erreur lors du déplacement du fichier.";
      }

    } else {
      $error = "Type de fichier non autorisé.";
    }

  } else {
    $error = "Le fichier est trop volumineux.";
  }
} else {
  if (isset($_FILES['file']['error']) && $_FILES['file']['error'] != UPLOAD_ERR_NO_FILE) {
    $error = "Erreur lors du téléchargement du fichier. Code d'erreur: " . $_FILES['file']['error'];
  }
}


if ((isset($_GET['id']) AND !empty($_GET['id'])) || (isset($_POST['id']) AND !empty($_POST['id']))) {
  $getid = isset($_GET['id']) ? $_GET['id'] : $_POST['id'];
  $recupUser= $bdd -> prepare('SELECT * FROM user WHERE id = ?');
  $recupUser -> execute(array($getid));
  if ($recupUser->rowCount() > 0){
    $destinataire = $recupUser->fetch();
} else {
    $error =  "Aucun utilisateur trouvé";
}

  if ($recupUser->rowCount() > 0){
      if (isset($_POST['envoyer'])){
          $message = htmlspecialchars($_POST['message']);

          // Ajout d'une condition pour vérifier si un fichier a été joint ou non
          if (!isset($_FILES['file']) || $_FILES['file']['error'] == UPLOAD_ERR_NO_FILE || $lastFileId !== null) {
              $insererMessage = $bdd->prepare('INSERT INTO message (message, id_destinataire, id_auteur, id_fichier) VALUES (?,?,?,?)');
              $insererMessage->execute(array($message, $getid, $_SESSION['id'], $lastFileId));
              $errorInfo = $insererMessage->errorInfo();

              header('Location: message.php?id=' . $getid);
          } else {
            $error = "Erreur lors de l'envoi du fichier : " . $error;
          }
      }
  }else{
      $error = "Aucun utilisateur trouvé33";
  }
}else{
  $error = "Aucun identifiant trouvé11";
}

?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">

  <title>Mily - Message privé</title>

  <!-- CSS de Bootstrap -->
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

  <!-- JavaScript/jQuery de Bootstrap (nécessite Popper.js) -->
  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
  <link rel="stylesheet" href="message.css">
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">

</head>



<body>
<div class="container_bis">
    <div class="row">
      <div class="col-12">
        <h2 class="text-center mt-3 mb-3" style="color: gray;">Messagerie privée</h2>
        <h1><?php echo $error?></h1>
        <div class="card">
          <div class="card-header custom-card-header">
            <div class="d-flex justify-content-between">
              <span class="destinataire">
                <h5 class="mt-2" style="font-family: 'Roboto', sans-serif;">Destinataire : <?= isset($destinataire['username']) ? $destinataire['username'] : ''; ?></h5>
              </span>
              <span>
                <h5 class="mt-2" style="font-family: 'Roboto', sans-serif;"><span class="status-circle"></span> Connecté : <?= $_SESSION['username']; ?></h5>
              </span>
            </div>
          </div>
          <div class="card-body">
            <div id="messages" class="mb-4">


      <?php

$recupMessages = $bdd->prepare('SELECT * FROM message WHERE id_auteur = ? AND id_destinataire = ? OR id_auteur = ? AND id_destinataire = ?');
$recupMessages->execute(array($_SESSION['id'], $getid, $getid, $_SESSION['id']));

      
while ($message = $recupMessages->fetch()) {
  $author = $message['id_auteur'] == $_SESSION['id'] ? $_SESSION['username'] : $destinataire['username'];
  $initial = strtoupper(substr($author, 0, 2));
  if ($message['id_destinataire'] == $_SESSION['id']) {
?>
      <p class="message message-incoming">
          <?= $message['message']; ?>
          <?php
          if ($message['id_fichier'] !== null) {
            $fichier = $bdd->prepare('SELECT chemin FROM fichiers WHERE id = ?');
            $fichier->execute(array($message['id_fichier']));
            $file = $fichier->fetch(PDO::FETCH_ASSOC);
            echo '<br><a href="' . $file['chemin'] . '" download>Télécharger le fichier</a>';
        }
        
          ?>
          <span class="circle circle-incoming"><?= $initial; ?></span>
      </p>
<?php
  } elseif ($message['id_destinataire'] == $getid) {
?>
      <p class="message message-outgoing">
          <?= $message['message']; ?>
          <?php
          if ($message['id_fichier'] !== null) {
            $fichier = $bdd->prepare('SELECT chemin FROM fichiers WHERE id = ?');
            $fichier->execute(array($message['id_fichier']));
              $file = $fichier->fetch();
              echo '<br><a href="' . $file['chemin'] . '" download>Télécharger le fichier</a>';
          }
          ?>
          <span class="circle circle-outgoing"><?= $initial; ?></span>
      </p>
      
<?php
  }
}
    
      ?>
      </div>
     </div>

            <form method="POST" action="" enctype="multipart/form-data">
              <div class="form-group">
                <label for="message">Votre message :</label>

                <div style = "display:flex;">
                  <textarea name="message" id="message" class="form-control"></textarea>
                  <label for="file" class="btn btn-secondary">
                    <span class="fas fa-paperclip"></span>
                   
                  </label>
                </div>
                <input type="file" name="file" id="file" class="form-control">
                
              </div>
              
  

      <input type="submit" name="envoyer" class="btn btn-primary">
    </form>

    <div class="bottom-button-container">
        <a href="discussion.php" class="btn btn-outline-secondary">
          <span>&larr;</span> Retour aux utilisateurs
        </a>
    </div>
</div>
</div>
</div>
</div>
</div>
</body>
</html>
