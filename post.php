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

$rosca=mysqli_query($db,"SELECT * FROM rosca JOIN groupe ON rosca.id=groupe.id_rosca JOIN user ON user.id=groupe.id_user WHERE user.username=$username");
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
<?php include('foot.php') ?>