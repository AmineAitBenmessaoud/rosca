<?php include('server.php') ?>
<?php
	if(isset($_GET['add_rosca'])){
		$type=mysqli_real_escape_string($db, $_GET['type']);
		$amount=mysqli_real_escape_string($db, $_GET['amount']);
		$participant=mysqli_real_escape_string($db, $_GET['participant']);
		$periodicity=mysqli_real_escape_string($db, $_GET['periodicity']);
		$month=mysqli_real_escape_string($db, $_GET['month']);
		$comment=mysqli_real_escape_string($db, $_GET['comment']);
		$id_user=$_SESSION['id'];
		mysqli_query($db,"INSERT INTO rosca (type,amount,periodicity,participant,comment) VALUES('$type','$amount','$periodicity','$participant','$comment')");
		$lastID = mysqli_insert_id($db);
		printf($id_user);
		mysqli_query($db,"INSERT INTO groupe (id_rosca,id_user,month) VALUES('$lastID','$id_user','$month');");
		/*mysqli_query($db,"CREATE TABLE $newc(
    week INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    debut DATE NOT NULL,
    fin DATE NOT NULL
	)");*/
		header('location: post.php');
	}
	if(isset($_POST['add_groupe'])){
		$id_user=$_SESSION['id'];
		$month=mysqli_real_escape_string($db, $_POST['month']);
		$rosca=mysqli_real_escape_string($db, $_GET['id_rosca']);
		printf($rosca,$month);
		mysqli_query($db,"INSERT INTO groupe (id_rosca,id_user,month) VALUES('$rosca','$id_user','$month');");
		mysqli_query($db,"UPDATE rosca SET actual_participants=actual_participants+1 WHERE id='$rosca'");
		/*mysqli_query($db,"CREATE TABLE $newc(
    week INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    debut DATE NOT NULL,
    fin DATE NOT NULL
	)");*/
		header('location: post.php');
	}
?>