<?php
require_once "pdo_skp.php";
session_start();
$salt = 'XyZzy12*_';

/*if(!isset($_SESSION['id']) || !isset($_SESSION['mail']))
{
	session_destroy();
	die('ACCESS DENIED');
}*/
if(!isset($_REQUEST['id']))
{
	session_destroy();
	die('ACCESS DENIED');
}
else if(isset($_REQUEST['id']))
{
	unset($_SESSION['id']);
	if(isset($_POST['id']) && isset($_POST['n_pw']) && isset($_POST['r_pw']))
	{
		if(strlen($_POST['id'])<1 || strlen($_POST['n_pw'])<1 || strlen($_POST['r_pw'])<1)
		{
			$_SESSION['failure']='All fields are required';
			header('Location: reset_password.php?id='.$_REQUEST['id']);
			return;
		}
		else
		{
			$hashed_npw = hash('md5', $salt.$_POST['n_pw']);//hashing the new password
			$hashed_rpw = hash('md5', $salt.$_POST['r_pw']);//hashing the retyped password
			if($hashed_rpw===$hashed_npw)
			{
				if($_SESSION['type']===0)//if its admin
				{
					$haid = hash('md5', $salt.$_POST['id']);//hashed admin id
					if($haid===$_REQUEST['id'])
					{
						$stmt = $pdo->prepare("UPDATE admin SET a_pw = :pw where a_id = :id");
						$row = $stmt->execute(array(':pw' => $hashed_npw, ':id' => $_POST['id']));
						if($row)
						{
							$_SESSION['success']='Successfully updated your password. Login now';
							header('Location: index.php');
							return;
						}
						else
						{
							$_SESSION['failure']='Unable to update your password due to invalid credentials. Try again';
							header('Location: forgot_password.php');
							return;	
						}
					}
					else
					{
						$_SESSION['failure']='Unable to update your password due to mismatch of user id with the expected user id. Kindly recheck your user id and try again';
						header('Location: forgot_password.php');
						return;	
					}
				}
				else if($_SESSION['type']===1)
				{
					$huid = hash('md5', $salt.$_POST['id']);//hashed user id
					if($huid===$_REQUEST['id'])
					{
						$stmt = $pdo->prepare("UPDATE users SET u_pw = :pw where u_id = :id");
						$row = $stmt->execute(array(':pw' => $hashed_npw, ':id' => $_POST['id']));
						if($row)
						{
							$_SESSION['success']='Successfully updated your password. Login now';
							header('Location: index.php');
							return;
						}
						else
						{
							$_SESSION['failure']='Unable to update your password due to invalid credentials. Try again';
							header('Location: forgot_password.php');
							return;	
						}
					}
					else
					{
						$_SESSION['failure']='Unable to update your password due to mismatch of user id with the expected user id. Kindly recheck your user id and try again';
						header('Location: forgot_password.php');
						return;	
					}
				}
				else
				{
					$_SESSION['failure']='Server error. Try again.';
					header('Location: forgot_password.php');
					return;
				}
			}	
			else
			{
				$_SESSION['failure']='Retyped password does not match new password. Try again';
				header('Location: reset_password.php?id='.$_REQUEST['id']);
				return;
			}
		}	
	}
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Forgot password</title>
<link rel="stylesheet" href="css/bootstrap.min.css">
<link rel="stylesheet" href="css/detail.css">
</head>
<body>
<div class="container">
<h1>GPS Tracking System</h1><hr>
<h2> Password Reset
<?php
//if(isset($_GET['id']))
//{
/*$stmt = $pdo->prepare("SELECT a_name FROM admin WHERE a_id=:id");
$stmt->execute(array(':id' => $_REQUEST['id']));
$row1 = $stmt->fetch(PDO::FETCH_ASSOC);
if($row1)
{
	$_SESSION['type'] = 0;
	echo(" Admin ".$row1['a_name']."</h2>");
}
else
{
	$stmt = $pdo->prepare("SELECT u_name FROM users WHERE u_id=:id");
	$stmt->execute(array(':id' => $_REQUEST['id']));
	$row2 = $stmt->fetch(PDO::FETCH_ASSOC);
	if($row2)
	{
		$_SESSION['type'] = 1;//for user
		echo(" User ".$row2['u_name']."</h2>");
	}
	else
	{
		echo($_REQUEST['id']." No such user id exists. Kindly recheck your verification email or contact gps_tacker_website");
	}
}*/
if(isset($_SESSION['failure'])) {
    echo('<p style="color: red;">'.htmlentities($_SESSION['failure'])."</p>\n");
    unset($_SESSION['failure']);
}
else if(isset($_SESSION['success']))
{
	echo('<p style="color: blue;">'.htmlentities($_SESSION['success'])."</p>\n");
	//echo  $_SESSION['check'];
	//echo $_SESSION['type'];
    unset($_SESSION['success']);	
}
//}
?>
<form method="post">
<p>User Id:<input type="text" name="id" size="40" value=""/></p>
<p>New password:<input type="password" name="n_pw" size="30" value=""/></p>
<p>Retype new password:<input type="password" name="r_pw" size="30" value=""/></p>
<input type="submit" value="Save">
</form>
</div>
</body>
</html>