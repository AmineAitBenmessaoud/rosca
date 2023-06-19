<?php
//connexion base de données
include('server.php');

// Vérification de la méthode de requête
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupération des données du formulaire
    $username = isset($_POST['username']) ? $_POST['username'] : "";
    $password = isset($_POST['password']) ? password_hash($_POST['password'], PASSWORD_BCRYPT) : "";

    // Vérification que les données nécessaires sont présentes
    if (empty($username) || empty($password) ) {
        die("Erreur : certaines données sont manquantes.");
    }

    // Stockage des données dans la session
    $_SESSION['username'] = $username;
    $_SESSION['password'] = $password;
   
    // Verification de l'existence du nom d'utilisateur
    $query = "SELECT username FROM `user` WHERE username=?";
    $stmt = mysqli_prepare($db, $query);
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if (mysqli_num_rows($result) != 0) {
        //le nom d'utilisateur existe déjà
        $error = "Un utilisateur avec le même nom d'utilisateur existe déjà.";
        header("Location: register.php?error=".$error); # on redirige vers register en précisant cette erreur
        exit();
    } 

    //ajout user
 
    $verified = 1; // ou la valeur que vous voulez initialiser
    $photoindex = 'default'; // ou la valeur que vous voulez initialiser
    
    $query = "INSERT INTO `user` (`username`, `password`, `verified`, `photoindex`) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($db, $query);
    mysqli_stmt_bind_param($stmt, "ssis", $username, $password, $verified, $photoindex);
    $success = mysqli_stmt_execute($stmt);
    
    
    if ($success) {
        $user_id = mysqli_insert_id($db);
        $_SESSION['id'] = $user_id;
    } else {
        $error = mysqli_error($db);
        die("Erreur lors de l'insertion du nouvel utilisateur: " . $error);
    }
    


    // Récupération de l'ID du nouvel utilisateur
    $query = "SELECT id FROM user WHERE username=?";
    $stmt = mysqli_prepare($db, $query);
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($result) {
        $row = mysqli_fetch_row($result);
        if ($row) {
            $_SESSION['id'] = intval($row[0]);
            $user_id = $_SESSION['id'];

        } else {
            die("Aucun résultat pour cette requête : " . $query);
        }
    } else {
        die("Erreur lors de l'exécution de la requête : " . $query);
    }
}

    






else {

    // Affichage du formulaire
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<title>ROSCA</title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
<!--===============================================================================================-->	
	<link rel="icon" type="image/png" href="images/icons/favicon.ico"/>
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="vendor/bootstrap/css/bootstrap.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="fonts/font-awesome-4.7.0/css/font-awesome.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="fonts/Linearicons-Free-v1.0.0/icon-font.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="vendor/animate/animate.css">
<!--===============================================================================================-->	
	<link rel="stylesheet" type="text/css" href="vendor/css-hamburgers/hamburgers.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="vendor/animsition/css/animsition.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="vendor/select2/select2.min.css">
<!--===============================================================================================-->	
	<link rel="stylesheet" type="text/css" href="vendor/daterangepicker/daterangepicker.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="css/util.css">
	<link rel="stylesheet" type="text/css" href="css/main.css">
<!--===============================================================================================-->
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
<style type="text/css">
	  .links{
    ul 
	{
		list-style-type: none;
	}
    li a{
      color: white;
      transition: color .2s;
      &:hover{
        text-decoration:none;
        color:#4180CB;
        }
    }
  }  
  .about-company{
    i{font-size: 25px;}
    a{
      color:white;
      transition: color .2s;
      &:hover{color:#4180CB}
    }
  } 
  .location{
    i{font-size: 18px;}
  }
  .copyright p{border-top:1px solid rgba(255,255,255,.1);} 
}
</style>

</head>
<body>
	
	<div class="limiter">	

		<div class="container-login100" style="background-image: url('images/bg-01.jpg');">
			
			<div class="wrap-login100 p-l-110 p-r-110 p-t-62 p-b-33">

				<form class="login100-form validate-form flex-sb flex-w" method="post" action="register.php">
					
                <
                
                
                <span class="login100-form-title p-b-53">
                Commencons votre inscription !
					</span>	
					

					<div class="p-t-31 p-b-9">
                        
						<span class="txt1">
							Nom d'utilisateur:
						</span>
					</div>
					<div class="wrap-input100 validate-input" data-validate = "Nom d'utilisateur est nécessaire">
						<input class="input100" type="text" name="username">
						<span class="focus-input100"></span>
					</div>
					
					<div class="p-t-13 p-b-9">
						<span class="txt1">
							Mot de passe:
						</span>
					</div>
					<div class="wrap-input100 validate-input" data-validate = "Mot de passe est nécessaire">
                    <input class="input100" type="password" name="password">
						<span class="focus-input100"></span>
					</div>
					<?php include('errors.php'); ?>
					<div class="container-login100-form-btn m-t-17">
						<button class="login100-form-btn" name="login_user" type="submit">
							S'inscrire
						</button>
					</div>
					</div>
					
				</form>
			</div>
		</div>
	</div>
	

	<div id="dropDownSelect1"></div>
	
<!--===============================================================================================-->
	<script src="vendor/jquery/jquery-3.2.1.min.js"></script>
<!--===============================================================================================-->
	<script src="vendor/animsition/js/animsition.min.js"></script>
<!--===============================================================================================-->
	<script src="vendor/bootstrap/js/popper.js"></script>
	<script src="vendor/bootstrap/js/bootstrap.min.js"></script>
<!--===============================================================================================-->
	<script src="vendor/select2/select2.min.js"></script>
<!--===============================================================================================-->
	<script src="vendor/daterangepicker/moment.min.js"></script>
	<script src="vendor/daterangepicker/daterangepicker.js"></script>
<!--===============================================================================================-->
	<script src="vendor/countdowntime/countdowntime.js"></script>
<!--===============================================================================================-->
	<script src="js/main.js"></script>
<div class="footer" style="background-color: #314759;">
<div class="container">
  <div class="row">
    <div class="col-lg-5 col-xs-12 about-company">
      <!-- <a href="../"><img src="images/logo.png" style="margin-top:10px;"></a>
      <p style="color:white;">Vous pouvez nous contacter pour intégrer votre CPGE dans la plateforme et pour plus d'informations</p> -->
    </div>
    <div class="col-lg-4 col-xs-12 location">
      <!-- <h3 style="color: white;">Contactez-nous :</h3>
      <p style="color: white;"><i class="fa fa-envelope-o mr-3"></i>info@cpgek.com</p> -->
    </div>
  </div>
  <div class="row mt-5">
    <!-- <div class="col copyright">
      <p class="" style="color: white;"><small class="text-white-50">© 2023 | ROSCA  <b>version beta</b></small></p>
    </div> -->
  </div>
</div>
</div>
</body>
</html>
<?php
} 





