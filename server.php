<?php 
	session_start();
	// variable declaration
	$username = "";
	$errors = array(); 
	$_SESSION['success'] = "";
	// connect to database
	//$db = mysqli_connect('localhost','u593112326_rosca', 'c=!k6eO7~', 'u593112326_rosca');
	//$db = mysqli_connect('localhost','root', '', 'u593112326_rosca');
		$db = mysqli_connect('localhost','root', 'root', 'u593112326_rosca',3306);


	// Change password USER
	if (isset($_POST['ch_pass'])) {
		// receive all input values from the form
		$password_1 = mysqli_real_escape_string($db, $_POST['password_1']);
		$password_2 = mysqli_real_escape_string($db, $_POST['password_2']);
		$password_3 = mysqli_real_escape_string($db, $_POST['password_3']);
		// form validation: ensure that the form is correctly filled
		if (empty($password_1)) { array_push($errors, "L'ancien mot de passe est nécessaire"); }
		if (empty($password_2)) { array_push($errors, "Le nouveau mot de passe est nécessaire"); }
		if (empty($password_3)) { array_push($errors, "Confirmer le nouveau mot de passe"); }
		if ($password_2 != $password_3) {
			array_push($errors, "Les deux mot de passe ne correspondent");
		}
		// the old password verification 
		if (count($errors) == 0) {
			$username=$_SESSION['username'];
			$query = "SELECT * FROM users WHERE username='$username'";
			$results = mysqli_query($db, $query);
			if (mysqli_num_rows($results) == 1) {
				$username = $_SESSION['username'];
				while($row = mysqli_fetch_array($results))
				{
					$passmode=$row['password'];
				}
				if(password_verify($password_1, $passmode)){
					$password=password_hash($_POST['password_2'], PASSWORD_ARGON2I);
					$query = "UPDATE users SET password='$password' WHERE username='$username'";
					mysqli_query($db, $query);
					header('location: index.php');
		        }
		        else {
				array_push($errors, "Mauvaise combinaison du ancien mot de passe");
			}
		}
	}
}
	// LOGIN USER
	if (isset($_POST['login_user'])) {
		$username = mysqli_real_escape_string($db, $_POST['username']);
		$password = mysqli_real_escape_string($db, $_POST['password']);

		if (empty($username)) {
			array_push($errors, "Nom d'utilisateur est nécessaire");
		}
		if (empty($password)) {
			array_push($errors, "Mot de passe est nécessaire");
		}

		if (count($errors) == 0) {
			$query = "SELECT * FROM user WHERE username='$username'";
			$results = mysqli_query($db, $query);
			if (mysqli_num_rows($results) == 1) {
				while($row = mysqli_fetch_array($results))
				{
					$passmode=$row['password'];
					$id=$row['id'];
				}
				if(password_verify($password, $passmode)){
				$_SESSION['username'] = $username;
				$_SESSION['id'] = $id;
				$_SESSION['user_id'] = $id;
				$_SESSION['success'] = "You are now logged in";
				header('location: post.php');
			}else {
				array_push($errors, "Nom d'utilisateur ou mot de passe incorrect !");
			}
		}else{
			array_push($errors, "Nom d'utilisateur ou mot de passe incorrect !");
		}
	}
}
?>