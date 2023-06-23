<?php include('server.php') ?>
<?php
//update semaine
	if(isset($_GET['add_semaine'])){
		$debut=mysqli_real_escape_string($db, $_GET['debut']);
		$fin=mysqli_real_escape_string($db, $_GET['fin']);
		$semaine=mysqli_real_escape_string($db, $_GET['semaine']);
		mysqli_query($db,"UPDATE semaine SET debut='$debut',fin='$fin' WHERE week='$semaine'");
		header('location: planadmin.php');
	}           
	//add séance 
	if(isset($_GET['add_seance'])){
		$seance=mysqli_real_escape_string($db, $_GET['seance']);
		$subject=mysqli_real_escape_string($db, $_GET['subject']);
		$jour=mysqli_real_escape_string($db, $_GET['jour']);
		$debut1=mysqli_real_escape_string($db, $_GET['debut1']);
		$fin1=mysqli_real_escape_string($db, $_GET['fin1']);
		$prof=mysqli_real_escape_string($db, $_GET['prof']);
		$salle=mysqli_real_escape_string($db, $_GET['salle']);
		$just="seance_".$classe;
		mysqli_query($db,"INSERT INTO $just (seance,subject,debut,debut_time,fin_time,prof,salle) VALUES('$seance','$subject','$jour','$debut1','$fin1','$prof','$salle')");
		header('location: planadmin.php');
	} 
	//delete seance
		if(isset($_GET['delete_seance'])){
		$seance=mysqli_real_escape_string($db, $_GET['seance']);	
		$just="seance_".$classe;
		mysqli_query($db,"DELETE FROM $just WHERE seance='$seance'");
		header('location: planadmin.php');
	}
		//add secret 
	if(isset($_GET['add_him'])){
		$user=mysqli_real_escape_string($db, $_GET['user']);
		$clas=mysqli_real_escape_string($db, $_GET['clas']);
		$gro=mysqli_real_escape_string($db, $_GET['gro']);
		$password=password_hash('Am1234Am1234', PASSWORD_BCRYPT);
		$just="note_".$clas;
		$type="eleve";
		mysqli_query($db,"INSERT INTO users (type,groupe,username,password,classe) VALUES('$type','$gro','$user','$password','$clas')");
		mysqli_query($db,"INSERT INTO $just (student,groupe,matiere,C1,C2,C3,C4,C5,C6,C7,C8,C9,C10,C11,C12) VALUES('$user','$gro','MA','N','N','N','N','N','N','N','N','N','N','N','N')");
		mysqli_query($db,"INSERT INTO $just (student,groupe,matiere,C1,C2,C3,C4,C5,C6,C7,C8,C9,C10,C11,C12) VALUES('$user','$gro','PC','N','N','N','N','N','N','N','N','N','N','N','N')");
		mysqli_query($db,"INSERT INTO $just (student,groupe,matiere,C1,C2,C3,C4,C5,C6,C7,C8,C9,C10,C11,C12) VALUES('$user','$gro','ANG','N','N','N','N','N','N','N','N','N','N','N','N')");
		mysqli_query($db,"INSERT INTO $just (student,groupe,matiere,C1,C2,C3,C4,C5,C6,C7,C8,C9,C10,C11,C12) VALUES('$user','$gro','FR','N','N','N','N','N','N','N','N','N','N','N','N')");

		 $tsi=preg_replace('/[0-9]+/','',$clas);
 		if($tsi=="tsi"){
				mysqli_query($db,"INSERT INTO $just (student,groupe,matiere,C1,C2,C3,C4,C5,C6,C7,C8,C9,C10,C11,C12) VALUES('$user','$gro','GE','N','N','N','N','N','N','N','N','N','N','N','N')");
			mysqli_query($db,"INSERT INTO $just (student,groupe,matiere,C1,C2,C3,C4,C5,C6,C7,C8,C9,C10,C11,C12) VALUES('$user','$gro','GM','N','N','N','N','N','N','N','N','N','N','N','N')");
	}
		header('location: secret.php');
	} 
	//delete secret
		if(isset($_GET['delete_him'])){
		$user=mysqli_real_escape_string($db, $_GET['user']);
		$result = mysqli_query($db,"SELECT * FROM users WHERE username='$user'");
		while($row = mysqli_fetch_array($result))
{
	$just="note_".$row['classe'];
}
		mysqli_query($db,"DELETE FROM $just WHERE student='$user'");
		mysqli_query($db,"DELETE FROM users WHERE username='$user'");
		header('location: secret.php');
	}
	//add classe
	if(isset($_GET['add_rosca'])){
		$type=mysqli_real_escape_string($db, $_GET['type']);
		$amount=mysqli_real_escape_string($db, $_GET['amount']);
		$participant=mysqli_real_escape_string($db, $_GET['participant']);
		$periodicity=mysqli_real_escape_string($db, $_GET['periodicity']);
		$month=mysqli_real_escape_string($db, $_GET['newc']);
		$id_user=$_SESSION['id'];
		mysqli_query($db,"INSERT INTO rosca (type,amount,periodicity,participant) VALUES('$type','$amount','$periodicity','$participant')");
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
	if(isset($_GET['add_groupe'])){
		$id_user=$_SESSION['id'];
		$id_rosca=$_SESSION['id_rosca'];
		$month=mysqli_real_escape_string($db, $_GET['month']);
		mysqli_query($db,"INSERT INTO groupe (id_rosca,id_user,month) VALUES('$id_rosca','$id_user','$month');");
		/*mysqli_query($db,"CREATE TABLE $newc(
    week INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    debut DATE NOT NULL,
    fin DATE NOT NULL
	)");*/
		header('location: post.php');
	}
			if(isset($_GET['delete_classe'])){
		$newc=mysqli_real_escape_string($db, $_GET['newc']);
		$classnote="note_".$newc;
		$seance="seance_".$newc;
		mysqli_query($db,"DROP TABLE $newc");
		mysqli_query($db,"DROP TABLE $classnote");
		mysqli_query($db,"DROP TABLE $seance");
		mysqli_query($db,"DELETE FROM groupe WHERE classe='$newc'");
		mysqli_query($db,"DELETE FROM users WHERE classe='$newc'");
		header('location: secret.php');
	}
	//evaluation 
	if(isset($_GET['save'])){		
		$classnote=$_SESSION['classenote'];
		$student=$_SESSION['student'];
		$colle=$_SESSION['colle'];
		$matiere=$_SESSION['matiere'];
		$number=str_replace('C','',$colle);
		$reval='CR'.$number;
		$note=$_GET['note'];
		$remarque=$_GET['remarque'];
		$result=mysqli_query($db,"UPDATE $classnote SET $colle='$note' WHERE student='$student' AND matiere='$matiere'");
		$result=mysqli_query($db,"UPDATE $classnote SET $reval='$remarque' WHERE student='$student'  AND matiere='$matiere'");
		$small="location: eval.php?colle=".$colle."&classenote=".$classnote.'&student='.$student.'&matiere='.$matiere;
		header($small);
	}
	//auto-evaluation 
		if(isset($_GET['saveauto'])){		
		$classnote=$_SESSION['classenote'];
		$student=$_SESSION['student'];
		$colle=$_SESSION['colle'];
		$matiere=$_SESSION['matiere'];
		$number=str_replace('C','',$colle);
		$auto='CA'.$number;
		$rauto='CAR'.$number;
		$note=$_GET['note'];
		$remarque=$_GET['remarque'];
		$result=mysqli_query($db,"UPDATE $classnote SET $auto='$note' WHERE student='$student' AND matiere='$matiere'");
		$result=mysqli_query($db,"UPDATE $classnote SET $rauto='$remarque' WHERE student='$student' AND matiere='$matiere'");
		$small="location: autoeval.php?colle=".$colle."&classenote=".$classnote.'&student='.$student.'&matiere='.$matiere;
		header($small);
	}
?>