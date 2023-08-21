<?php include('server.php') ?>
<?php 
$username=$_SESSION['username'];
	if (!isset($_SESSION['username'])) {
		$_SESSION['msg'] = "You must log in first";
		header('location: index.php');
	}
	if (isset($_GET['logout'])) {
		session_destroy();
		unset($_SESSION['username']);
		header("location: index.php");
	}

?>
<!DOCTYPE html>
<html>
<head>
	<title>Post</title>
	<?php include('head.php') ?>
	</head>
	
		<?php include('bar.php') ?>
		<nav>
      <input type="checkbox" id="check">
      <label for="check" class="checkbtn">
        <i class="fas fa-bars"></i>
      </label>
	  <!-- <label class="logo"><img src="images/logo.png" height="85px" style="padding-bottom:10px;"></label> -->
      <ul>
        <li><a class="active" href="post.php">Post</a></li>
      </ul>
    </nav>  
<?php
echo"
<div class='glory'><h3 style='color:#9933ff;'>Rosca</h3>
<div style='overflow-x:auto;'>";
$id_user=$_SESSION['id'];
$groups=mysqli_query($db,"SELECT id_rosca FROM groupe WHERE id_user='$id_user'");
$id_rosca_actually_in=array();
while($row_groups =mysqli_fetch_array($groups)){
	array_push($id_rosca_actually_in,$row_groups['id_rosca']);
}
$rosca=mysqli_query($db,"SELECT * FROM rosca");
while($row = mysqli_fetch_array($rosca)){
	if($row['participant']>$row['actual_participants'] && ! in_array($row['id'],$id_rosca_actually_in) ){
	$id_rosca=$row['id'];
	$_SESSION['id_rosca']=$id_rosca;
	echo "<div id='rosca_box' style='border: thick double #32a1ce;'>";
	echo "<h2 style='color:blue;'>Rosca Numéro ".$row['id']."</h2>";
	if($row['type']==1){echo "Traditionnel";
	}else{
		echo "</br><p> Type : Parallèle</p>";
	}
	echo "</br><p>Le montant :".$row['amount']."</p>";
	if($row['periodicity']==1){echo "</br><p> Type : Mensuelle</p>";
	}else{
		echo "</br><p> Type : Bimensuelle</p>";
	}
	echo "</br><p>Nombre de place libre : ".$row['participant']-$row['actual_participants']."</p>";
	echo "</br><p> commentaire :".$row['comment']."</p>";
 	echo "<form method='post' action='action.php?id_rosca=".$id_rosca."'>"; ?>
	<h4>Choose the month :</h4>
	<input type="number" min="1" value="1" max="12" name="month" style="width: 100px;" class="form-control">
	<?php
	echo "<button  class='btn btn-outline-primary' name='add_groupe'>Add to the groupe</button>
	</form>";
 echo "</div>";}
}
echo"</div>";
?>

			<h2 style="color: #330066; margin-top: 20px;"><strong>Ajouter un post:</strong></h2>
		<form method="get" action="action.php"> 
	<div>
	<h4>Type</h4>
<select name="type">
  <option value="1">trad</option>
  <option value="2">para</option>
</select>
<h4>amount</h4>
<input type="number" min="1" value="1" name="amount" style="width: 100px;" class="form-control">
	<h4> de participant</h4>
<input type="number" min="1" value="1" name="participant" style="width: 100px;" class="form-control">

	<h4>Périodicité</h4>
	<select name="periodicity">
  <option value="1">monsuelle</option>
  <option value="2">bimensuelle</option>
  <option value="3">trimestrielle</option>
</select>
<h4>Date souhaitée</h4>
<input type="number" min="1" value="1" max="12" name="month" style="width: 100px;" class="form-control">

<h4>comment</h4>
<input type="text" name="comment" style="width: 100px; height :150 px;">
</div>
<button  class="btn btn-outline-primary" name="add_rosca">Add</button>
</form>
<br>
<?php include('foot.php') ?>