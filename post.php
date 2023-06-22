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
	  <label class="logo"><img src="images/logo.png" height="85px" style="padding-bottom:10px;"></label>
      <ul>
        <li><a class="active" href="post.php">Post</a></li>
      </ul>
    </nav>  
<?php
echo"
<h3 style='color:#9933ff;'>Rosca</h3>
<div style='overflow-x:auto;''><table  class='content-table' >
<thead>
<tr>
";
echo"<th>id</th>";
echo"<th>type</th>";
echo"<th>amount</th>";
echo"<th>periodicity</th>";
echo"
  </thead>";
echo "<tbody>";

$rosca=mysqli_query($db,"SELECT * FROM rosca JOIN groupe ON rosca.id = groupe.id_rosca JOIN user ON user.id = groupe.id_user WHERE user.username = '$username'");
while($row = mysqli_fetch_array($rosca)){
	echo "<tr>";
	echo "<td>".$row['id']."</td>";
	echo "<td>".$row['type']."</td>";
	echo "<td>".$row['amount']."</td>";
	echo "<td>".$row['periodicity']."</td>";
echo "</tr>";
}
echo "</tbody>";
echo"</table></div>";
?>
<div class="glory">
			<h2 style="color: #330066; margin-top: 20px;"><strong>Ajouter un post:</strong></h2>
		<form method="get" action="action.php"> 
	<div style='overflow-x:auto;'>
	<table class="content-table">
	<thead>
		<tr>
		<th>Type</th>
		<th>amount</th>
		<th>Nbr de participant</th>
		<th>Périodicité</th>
		<th>Date souhaitée</th>
	</tr>
	</thead>	
	<tbody>
		<tr>
	<td>
<select name="type">
  <option value="1">trad</option>
  <option value="2">para</option>
</select></td>
	<td><input type="number" min="1" value="1" name="amount" style="width: 100px;" class="form-control"></td>
	<td><input type="number" min="1" value="1" name="participant" style="width: 100px;" class="form-control"></td>
	<td>
<select name="periodicity">
  <option value="1">monsuelle</option>
  <option value="2">bimensuelle</option>
  <option value="3">trimestrielle</option>
</select></td>
<td>

<input type="number" min="1" value="1" max="12" name="month" style="width: 100px;" class="form-control">

</td>
		</tr>
</tbody>
</table></div>
<button  class="btn btn-outline-primary" name="add_rosca">Add</button>
</form>
<br>
<?php include('foot.php') ?>