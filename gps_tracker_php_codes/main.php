<?php
require_once "pdo_skp.php";
session_start();
if ( ! isset($_SESSION['id']) ) {
    die('ACCESS DENIED');
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Main Page</title>
<link rel="stylesheet" href="css/bootstrap.min.css">
<link rel="stylesheet" href="css/detail.css">
</head>
<body>
<div class="container">
<h1>GPS Tracking System</h1><hr>
<h2> Welcome 
<?php
if( isset($_SESSION['id']) && $_SESSION['type']===0){             //ADMIN Login
	$stmt = $pdo->prepare("SELECT a_name FROM admin WHERE a_id=:id");
	$stmt->execute(array(':id' => $_SESSION['id']));
	$row = $stmt->fetch(PDO::FETCH_ASSOC);
	echo(" Admin ".$row['a_name']."</h2>");
	echo("<p><a href='user_info.php'>Users</a></p>");
}
else if( isset($_SESSION['id']) && $_SESSION['type']===1){        //USER Login
	$stmt = $pdo->prepare("SELECT u_name FROM users WHERE u_id=:id");
	$stmt->execute(array(
						':id' => $_SESSION['id']));
	$row = $stmt->fetch(PDO::FETCH_ASSOC);
	echo(" User ".$row['u_name']."</h2>");
	if ( isset($_SESSION['success']) ) {
        echo('<p style="color: blue;">'.htmlentities($_SESSION['success'])."</p>\n");
        unset($_SESSION['success']);
    }
	echo('<p><a href="gps_info.php?u_id='.$_SESSION['id'].'">Your GPS Info</a></p>');
	echo('<p><a href="edit_user.php?u_id='.$_SESSION['id'].'">Edit your profile</a></p>');
}
?>
<form method="POST" action="logout.php">
<input type="submit" value="Log Out">
</form>
</div>
</body>
</html>