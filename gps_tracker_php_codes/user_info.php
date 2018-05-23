<?php
require_once "pdo_skp.php";
session_start();
if ( ! isset($_SESSION['id']) || $_SESSION['type']!==0 ) {
    die('ACCESS DENIED');
}
// If the user requested logout go back to index.php and if cancel then go to main.php
if (isset($_POST['cancel'])) {
    header('Location: main.php');
    return;
}

if (isset($_POST['log_out'])) {
	header('Location: logout.php');
	return;
}
?>
<!DOCTYPE html>
<html>
<head>
<title>User's info</title>
<link rel="stylesheet" href="css/bootstrap.min.css">
<link rel="stylesheet" href="css/detail.css">
</head>
<body>
<div class="container">
<h1>GPS Tracking System</h1><hr>
<h2> Welcome 
<?php
if( isset($_SESSION['id']) && $_SESSION['type']===0){             //ADMIN
	$stmt = $pdo->prepare("SELECT a_name FROM admin WHERE a_id=:id");
	$stmt->execute(array(
						':id' => $_SESSION['id']));
	$row = $stmt->fetch(PDO::FETCH_ASSOC);
	echo(" Admin ".$row['a_name']."</h2>");
}
?>
<h4> Users record</h4>
<?php
if (isset($_SESSION['failure'])) {
        echo('<p style="color: red;">'.htmlentities($_SESSION['failure'])."</p>\n");
        unset($_SESSION['failure']);
    }
	
if (isset($_SESSION['success'])) {
        echo('<p style="color: blue;">'.htmlentities($_SESSION['success'])."</p>\n");
        unset($_SESSION['success']);
}
if(isset($_SESSION['id'])) {
	$stmt = $pdo->query("SELECT * FROM users");
	if($output = $stmt->fetchAll(PDO::FETCH_ASSOC)){
		echo('<center><table border="1">'."\n");
		echo("<thead><tr>
				<th>&nbsp;User ID&nbsp;</th>
				<th>&nbsp;User Name&nbsp;</th>
				<th>&nbsp;User Password&nbsp;</th>
				<th>&nbsp;User Email&nbsp;</th>
				<th>&nbsp;Actions &nbsp;</th>
				</tr></thead>");
		foreach ($output as $output) {
			echo ("<tr>");
			echo ("<td><center>".htmlentities($output['u_id'])."</td>");
			echo ('<td><center><a href="gps_info.php?u_id='.$output['u_id'].'">'.htmlentities($output['u_name'])."</a></td>");//to link u_name with gps_info page
			//echo ("<td><center>".htmlentities($output['u_name'])."</td>");
			echo ("<td><center>".htmlentities($output['u_pw'])."</td>");
			echo ("<td><center>".htmlentities($output['u_email'])."</td>");
			echo ('<td><center><a href="edit_user.php?u_id='.$output['u_id'].'">&nbsp;Edit&nbsp;</a> / <a href="delete_user.php?u_id='.$output['u_id'].'">&nbsp;Delete&nbsp;</a></td>');
		}
		echo("</tr></table></center>");
	}
	else {
		echo ("No rows found!");
	}
}
?>
<form method="POST" >
<p><a href='add_user.php'>Add user</a></p>
<input type="submit" value="Log Out" name="log_out">
<input type="submit" value="Cancel" name="cancel">
</form>
</div>
</body>
</html>