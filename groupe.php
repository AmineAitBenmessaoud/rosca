<?php
session_start();
$error = "";
include('connect.php');
$lastFileId = null;
$destinataire = array();
$groupe = $_GET['groupe'] ?? $_POST['groupe'] ?? null;

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


if ((isset($_GET['groupe']) AND !empty($_GET['groupe'])) || (isset($_POST['groupe']) AND !empty($_POST['groupe']))) {
  $getid = isset($_GET['groupe']) ? $_GET['groupe'] : $_POST['groupe'];
  $recupUser= $bdd -> prepare('SELECT * FROM groupe WHERE id_rosca = ?');
  $recupUser -> execute(array($getid));
  if ($recupUser->rowCount() > 0){
    $destinataire = $recupUser->fetch(); //destinataire is the table of messages in the rosca_groupe
} else {
    $error =  "Aucun utilisateur trouvé";
}

  if ($recupUser->rowCount() > 0){
      if (isset($_POST['envoyer'])){
          $message = htmlspecialchars($_POST['message']);

          // Ajout d'une condition pour vérifier si un fichier a été joint ou non
          if (!isset($_FILES['file']) || $_FILES['file']['error'] == UPLOAD_ERR_NO_FILE || $lastFileId !== null) {
              $insererMessage = $bdd->prepare('INSERT INTO message (message, id_destinataire, id_auteur, id_fichier,id_rosca) VALUES (?,?,?,?,?)');
              $insererMessage->execute(array($message, 0, $_SESSION['id'], $lastFileId,$groupe));
              $errorInfo = $insererMessage->errorInfo();

              header('Location: groupe.php?groupe=' . $groupe);
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

  <title>Rosca_Messages</title>

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
        <h2 class="text-center mt-3 mb-3" style="color: gray;">Messagerie</h2>
        <h1><?php echo $error?></h1>
        <div class="card">
          <div class="card-header custom-card-header">
            <div class="d-flex justify-content-between">
            </div>
          </div>
          <div class="card-body">
            <div id="messages" class="mb-4">


      <?php

$recupMessages = $bdd->prepare('SELECT * FROM message WHERE id_rosca = ?');
//i was here
$recupMessages->execute(array($groupe));

      
while ($message = $recupMessages->fetch()) {
  $author = $message['id_auteur'] == $_SESSION['id'] ? $_SESSION['username'] : '0';
if( $author=='0'){
  $khona= $bdd -> prepare('SELECT * FROM user WHERE id = ?');
  $khona -> execute(array($message['id_auteur']));
  if ($khona->rowCount() > 0){
    $khoyamarouan = $khona->fetch(); 
  }
  $author=$khoyamarouan['username'];
  $initial = strtoupper(substr($author, 0, 2));
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
}elseif($author==$_SESSION['username']){
  //hania
  //hania 2
  $initial = strtoupper(substr($author, 0, 2));
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
        <a href="discussion_groupe.php" class="btn btn-outline-secondary">
          <span>&larr;</span> Retour aux groupe
        </a>
    </div>
</div>
</div>
</div>
</div>
</div>
</body>
</html>
