<?php
session_start();
include('connect.php');
if (!$_SESSION['username']){
    header('Location : ../index.html');
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Discussion grp</title>
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
    <a href='discussion.php'>Discussions privé</a>
  </div>
  <div class="container">
    <div class="form-group">
      <input type="text" id="search-user" class="form-control" placeholder="Rechercher un utilisateur">
    </div>
    <div class="list-group" id="search-results"></div>

  <?php
   $id= $_SESSION['id'];
  $recupUser = $bdd->query('SELECT * FROM user');
  $recupGroup = $bdd->query("SELECT * FROM groupe WHERE id_user='$id'");
//   while ($user = $recupUser->fetch()) {
//     if ($user['id'] !== $_SESSION['id']) {
//       // Vérifier si un message existe entre les deux utilisateurs
//       $checkMessage = $bdd->prepare('SELECT * FROM message WHERE (id_auteur = ? AND id_destinataire = ?) OR (id_auteur = ? AND id_destinataire = ?)');
//       $checkMessage->execute(array($_SESSION['id'], $user['id'], $user['id'], $_SESSION['id']));
//       $hasMessage = $checkMessage->rowCount() > 0;

//       // Si un message existe, affichez le profil de l'utilisateur
//       if ($hasMessage) {
//         $lastMessageQuery = $bdd->prepare('SELECT * FROM message WHERE (id_auteur = ? AND id_destinataire = ?) OR (id_auteur = ? AND id_destinataire = ?) ORDER BY id DESC LIMIT 1');
//         $lastMessageQuery->execute(array($_SESSION['id'], $user['id'], $user['id'], $_SESSION['id']));
//         $lastMessage = $lastMessageQuery->fetch();
//         $truncatedMessage = '';
//         if (is_array($lastMessage)) {
//             $truncatedMessage = mb_strimwidth($lastMessage['message'], 0, 30, '...');
//         }
while($groupe = $recupGroup->fetch()){
    echo "<a href='groupe.php?groupe=".$groupe['id_rosca']."'><h3>GROUPE Rosca ".$groupe['id_rosca']."</h3></a>";

}
//   ?>
       <!-- <a href="message.php?id=<?php echo $user['id']; ?>" class="list-group-item list-group-item-action">
       <div class="user-block">
        <img src="Photo1.jpg" alt="user-photo" class="user-photo" width="50" height="50">
          <div>
          <!-- <?php echo $user['username']; ?> - <small><?= $truncatedMessage; ?></small> -->
          </div>
        </div>
      </a> 
  <?php
//         }
//       }
//     }
    
//   ?>
</div>
    </div>
  </div>
  <script>
  $(document).ready(function() {
    // Écoutez les événements de saisie dans le champ de recherche
    $("#search-user").on("input", function() {
      const searchValue = $(this).val();
      
      if (searchValue) {
        // Envoyez une requête AJAX pour obtenir des suggestions d'utilisateur
        $.ajax({
          url: "search_user.php",
          type: "GET",
          data: { search: searchValue },
          success: function(data) {
            $("#search-results").html(data);
          }
        });
      } else {
        $("#search-results").empty();
      }
    });
  });
  </script>


</body>
</html>
