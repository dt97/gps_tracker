<?php
require_once "pdo_skp.php";
session_start();
if (!isset($_SESSION['id']) || $_SESSION['type']!==0) {//as only admin can edit gps info
    die('ACCESS DENIED');
}	
if (isset($_POST['cancel'])) {
	header('Location: gps_info.php?u_id='.$_REQUEST['u_id']);
    return;
}
// Guardian: Make sure that user_id is present
if (!isset($_GET['u_tstamp'])) {
	$_SESSION['failure'] = 'Invalid User ID';
	header('Location: gps_info.php?u_id='.$_REQUEST['u_id']);//to display list of valid user ids currently in database
	return;
}
$table = $_GET['u_id']."_account";
$stmt1 = $pdo->prepare("SELECT * FROM $table where u_tstamp = :xyz");
$stmt1->execute(array(":xyz" => $_GET['u_tstamp']));
$row1 = $stmt1->fetch(PDO::FETCH_ASSOC);
$stmt2 = $pdo->prepare("SELECT * FROM users where u_id = :xyz");
$stmt2->execute(array(":xyz" => $_GET['u_id']));
$row2 = $stmt2->fetch(PDO::FETCH_ASSOC);
if ( $row1 === false ) {
	$_SESSION['failure'] = 'Invalid User ID or invalid time stamp';
    header('Location: gps_info.php?u_id='.$_REQUEST['u_id']) ;
    return;
}
if ( $row2 === false ) {
	$_SESSION['failure'] = 'Invalid User ID';
    header('Location: gps_info.php?u_id='.$_REQUEST['u_id']) ;
    return;
}
$uid = htmlentities($row2['u_id']);
$utstamp = htmlentities($row1['u_tstamp']);
$utcord = htmlentities($row1['u_loc']);
$name = htmlentities($row2['u_name']);
if ( isset($_POST['newloc']) ){
	if( strlen($_POST['newloc']) < 1 ){
		$_SESSION['failure']='All fields are required';
		header( 'Location: edit_gps_info.php?u_id='.$_REQUEST['u_id'].'&u_tstamp='.$_REQUEST['u_tstamp'] ) ;
		return;
	}
	else
	{
		$table = $_POST['uid']."_account";
		//$salt = 'XyZzy12*_';
		//$hashed_pw = hash('md5', $salt.$_POST['pw']);	//store pw in hashed format 
		//$_SESSION['check']=$hashed_pw;
		$sql = "UPDATE $table SET 
    	        u_loc = :newloc
    	        WHERE u_tstamp = :utstamp";
    	$stmt = $pdo->prepare($sql);
    	$stmt->execute(array(
    	    ':newloc' => $_POST['newloc'],
    	    ':utstamp' => $_POST['utstamp']));
    	//$_SESSION['success'] = 'Record of teacher with ID '.$_POST["tid"].' is edited.';
		if($_SESSION['type']===1) {//when its a user who himself wants to edit his/her own profile
			$_SESSION['failure'] = 'Your are not allowed to update gps info.';
			header('Location: gps_info.php');
		}
		else {//if its admin
			$_SESSION['success'] = 'Record of user '.$_POST["name"].' with ID '.$_POST["uid"].' is edited for timestamp '.$_POST["utstamp"];
    	    header( 'Location: gps_info.php?u_id='.$_POST["uid"] ) ;//redirect to user_info.php
		}
    	return;
	}
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Editing user info</title>
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
	$admin = $stmt->fetch(PDO::FETCH_ASSOC);
	echo(" Admin ".$admin['a_name']."</h2>");
}
if ( isset($_SESSION['failure']) ) {
        echo('<p style="color: red;">'.htmlentities($_SESSION['failure'])."</p>\n");
        unset($_SESSION['failure']);
    }
?>
<h4>Edit user info with ID <?= $uid ?> for time stamp <?= $utstamp ?></h4>
<p>Old location coordinates <?= $utcord ?></p>
<form method="post">
<input type="hidden" name="uid" size="40" value="<?= $uid ?>"/>
<input type="hidden" name="name" size="40" value="<?= $name ?>"/>
<input type="hidden" name="utstamp" size="40" value="<?= $utstamp ?>"/>
<p>New location coordinates<input type="text" name="newloc" size="80" value=""/></p>
<input type="submit" value="Save">
<input type="submit" name="cancel" value="Cancel">
</form>
<p>
</div>
</body>
</html>