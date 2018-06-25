<?php
require_once "pdo_skp.php";
session_start();
$salt = 'XyZzy12*_';
if (isset($_POST['cancel'])) 
{
    header('Location: index.php');
    return;
}
if(isset($_POST['id']) && isset($_POST['email']))
{
	if(strlen($_POST['id'])<1 || strlen($_POST['email'])<1)
	{
		$_SESSION['failure']='All fields are required';
		header('Location: forgot_password.php');
		return;
	}
	else
	{
		$email =  "none";	
		$stmt = $pdo->prepare("SELECT a_email FROM admin where a_id=:id");
		$stmt->execute(array(':id' => $_POST['id']));
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		if($row===false)
		{
			$stmt = $pdo->prepare("SELECT u_email FROM users where u_id=:id");
			$stmt->execute(array(':id' => $_POST['id']));
			$row = $stmt->fetch(PDO::FETCH_ASSOC);
			if($row===false)
			{
				$_SESSION['failure']='No such user or admin exists.Please enter a valid id';
				header('Location: forgot_password.php');
			}
			else
			{
				$_SESSION['type'] = 1;//for user
				$email = $row['u_email'];
				if($_POST['email']===$email)
				{
					$_SESSION['success'] = "The password reset link has been sent to your registered email. Kindly check the verification link to reset your password.";
					header('Location: send_password_mail.php?id='.$_POST['id'].'&email='.$email);
				}
				else
				{
					$_SESSION['failure'] = "The email you entered didn't match with your registered email. Kindly, re-enter your email registered with our company. For any queries or issues visit our forum or contact us.";
					header('Location: forgot_password.php');
				}
			}
		}
		else
		{
			$_SESSION['type'] = 0;//for admin
			$email = $row['a_email'];
			if($_POST['email']===$email)
			{
				$_SESSION['success'] = "The password reset link has been sent to your registered email. Kindly check the verification link to reset your password.";
				header('Location: send_password_mail.php?id='.$_POST['id'].'&email='.$email);
			}
			else
			{
				$_SESSION['failure'] = "The email you entered didn't match with your registered email. Kindly, re-enter your email registered with our company. For any queries or issues visit our forum or contact us.";
				header('Location: forgot_password.php');
			}
		}
		return;
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
<?php
if(isset($_SESSION['failure']) ) {
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
?> 
<h4>Reset password</h4>
<form method="post">
<p>Your user id<input type="text" name="id" size="15" value=""/></p>
<p>Your email id<input type="email" name="email" size="50" value=""/></p>
<input type="submit" name='submit' value="Submit">
<input type="submit" name="cancel" value="Go back to login">
</form>
</div>
</body>
</html>