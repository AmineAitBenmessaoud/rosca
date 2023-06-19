<?php
// Démarrage de la session
session_start();
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


    $query = "SELECT username FROM `user` WHERE username='$username'";
    $result = mysqli_query($conn, $query);
    
    if (mysqli_num_rows($result) != 0) {
        //le nom d'utilisateur existe déjà
        $error = "un alumni avec le même nom d'utilisateur existe déjà.";
        header("Location : register.php?error=".$error); # on redirige vers register en précisant cette erreur
    } 

    
} 

else {

    // Affichage du formulaire
?>
<!DOCTYPE html>
<html>
<head>
    <title>Mily - Register</title>
    
    <!--Manrope-->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope&display=swap" rel="stylesheet">

    <!--Baloo 2-->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Baloo+2:wght@800&display=swap" rel="stylesheet">
    <link href="../mainStyle.css" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<style>
    
</style>

<body>

<header>
    <h1>Commencons votre inscription !</h1>
</header>
    <br />
    <div>
        <center>
        <?php
            echo $_GET['error'];
        ?>
        </center>
    </div>
    <div class="container_register">
        <div class="connexion">
            <center>
                <h2>S'inscrire</h2> <br/>
                <form method="post" action="register.php">
                <label for="username">Username:</label><br>
                <input type="text" id="username" name="username" required><br>

                <label for="password">Password:</label><br>
                <input type="password" id="password" name="password" required><br>

                <label>Profil:</label><br>
                <input type="radio" id="student" name="profile" value="student" required>
                <label for="student">Étudiant</label>
                <input type="radio" id="alumni" name="profile" value="alumni" required>
                <label for="alumni">Alumni</label><br>

                <input type="submit" value="Submit">
                    <br/>
                </form>      
            </center>
        
        </div>
    </div> 
    
</body>
</html>
<?php
} 



<?php
// Démarrage de la session
session_start();
//connexion base de données
include_once("../bd_connect.php");


// Vérification de la méthode de requête
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupération des données du formulaire
    $username = isset($_POST['username']) ? $_POST['username'] : "";
    $password = isset($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : "";
    $profile = isset($_POST['profile']) ? $_POST['profile'] : "";

    // Vérification que les données nécessaires sont présentes
    if (empty($username) || empty($password) || empty($profile)) {
        die("Erreur : certaines données sont manquantes.");
    }

    // Stockage des données dans la session
    $_SESSION['username'] = $username;
    $_SESSION['password'] = $password;
    $_SESSION['profile'] = $profile;


    $query = "SELECT username FROM `user` WHERE username='$username'";
    $result = mysqli_query($conn, $query);
    
    if (mysqli_num_rows($result) != 0) {
        //le nom d'utilisateur existe déjà
        $error = "un alumni avec le même nom d'utilisateur existe déjà.";
        header("Location : register.php?error=".$error); # on redirige vers register en précisant cette erreur
    } 

    // Redirection en fonction du profil vers le bon formulaire
    if ($profile == 'student') {
        header('Location: /register/student/student.php');
        exit();
    } elseif ($profile == 'alumni') {
        header('Location: /register/alumnis/form.php');
        exit();
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
					
                <label for="username">Username:</label><br>
                <input type="text" id="username" name="username" required><br>
                
                
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
						<input class="input100" type="password" name="pass" >
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





