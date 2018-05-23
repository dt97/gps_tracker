<?php
require_once "pdo_skp.php";
session_start();
if (!isset($_SESSION['id'])) {//as both user and admin can edit user profile there is no $_SESSION['type'] check
    die('ACCESS DENIED');
}	
if (isset($_POST['cancel'])) {
	if($_SESSION['type']===1)//if its a user
		header('Location: main.php');
	else//if its admin
		header('Location: user_info.php');//redirect to user_info.php where admin can access and edit user info
    return;
}
// Guardian: Make sure that user_id is present
if (!isset($_GET['u_id'])) {
	$_SESSION['failure'] = 'Invalid User ID';
	header('Location: user_info.php');//to display list of valid user ids currently in database
	return;
}
$stmt = $pdo->prepare("SELECT * FROM users where u_id = :xyz");
$stmt->execute(array(":xyz" => $_GET['u_id']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ( $row === false ) {
	$_SESSION['failure'] = 'Invalid User ID';
    header( 'Location: user_info.php' ) ;
    return;
}
$uid = htmlentities($row['u_id']);
$name = htmlentities($row['u_name']);
$pw = htmlentities($row['u_pw']);

if ( isset($_POST['uid']) && isset($_POST['name']) && isset($_POST['pw']) ){
	if( strlen($_POST['uid']) < 1 || strlen($_POST['name']) < 1 || strlen($_POST['pw']) < 1 ){
		$_SESSION['failure']='All fields are required';
		header( 'Location: edit_user.php?u_id='.$_REQUEST['u_id'] ) ;
		return;
	}
	else
	{
		$salt = 'XyZzy12*_';
		$hashed_pw = hash('md5', $salt.$_POST['pw']);	//store pw in hashed format 
		$_SESSION['check']=$hashed_pw;
		$email = $_POST['uemail'];
		if (!filter_var($email, FILTER_VALIDATE_EMAIL)) 
		{
  			$_SESSION['failure'] = "Invalid email format";
  			header( 'Location: edit_user.php?u_id='.$_REQUEST['u_id'] ) ; 
		}
		else
		{
			$sql = "UPDATE users SET 
          	u_name = :name, u_pw = :pw, u_email = :uemail
        	WHERE u_id = :uid";
    		$stmt = $pdo->prepare($sql);
    		$stmt->execute(array(
    		    ':uid' => $_POST['uid'],
    	   	 	':name' => $_POST['name'],
    	    	':pw' => $hashed_pw,
    	    	':uemail' => $_POST['uemail']));
    		//$_SESSION['success'] = 'Record of teacher with ID '.$_POST["tid"].' is edited.';
			if($_SESSION['type']===1) {//when its a user who himself wants to edit his/her own profile
				$_SESSION['success'] = 'Your profile has been updated.';
				header('Location: main.php');
			}
			else {//if its admin
				$_SESSION['success'] = 'Record of user '.$_POST["name"].' with ID '.$_POST["uid"].' is edited.';
    		    header( 'Location: user_info.php' ) ;//redirect to user_info.php
			}
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
<h4>Edit user info with ID <?= $uid ?></h4>
<form method="post">
<input type="hidden" name="uid" size="40" value="<?= $uid ?>"/>
<p>Name<input type="text" name="name" size="40" value="<?= $name ?>"/></p>
<p>Password<input type="password" name="pw" size="15" value=""/></p>
<p>Email<input type="email" name="uemail" size="15" value=""/></p>
<input type="submit" value="Save">
<input type="submit" name="cancel" value="Cancel">
</form>
<p>
</div>
</body>
</html>