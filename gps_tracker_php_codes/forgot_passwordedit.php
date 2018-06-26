<?php
require_once "pdo_skp.php";
session_start();
$salt = 'XyZzy12*_';
static $hid = "";
static $hemail = "";
if (isset($_POST['cancel'])) 
{
    header('Location: index.php');
    return;
}
function send_mail($hid)
{
	$_SESSION['id'] = $_POST['id'];
	//$hid = hash('md5', $salt.$_POST['id']);
	$message = 'http://skaipal.in/gps_tracker/reset_password.php?id='.$hid;
	$email_send = $_POST['email'];
	$email_from = 'info@skaipal.in';//<== update the email address
	$email_subject = "Password reset link for gps tracker website";
	$email_body = "You have received a new message regarding password reset link from $email_send \n".
		"Here is the password reset url:\n $message \n";
	$to = "sarojnayak@skaipal.in";//<== update the email address
	$headers = "From: $email_from \r\n";
	$headers .= "Reply-To: $email_send \r\n";
	//Send the email!	
	mail($to,$email_subject,$email_body,$headers);
	$email_from = $to;//<== update the email address
	$email_sub = "Received your password reset request for accessing gps tracker website!";
	$email_body = "We have received your password reset request for accessing gps tracker website. \n \n".
		"Here is the password reset url:\n $message \n;
	Regards, \n
	Skaipal Consulting Private Ltd. \n";
	$headers = "From: $email_from \r\n" ;
	$headers .="Reply-To: $to \r\n" ;
	if(mail($email_send,$email_sub,$email_body,$headers))
	{
		$_SESSION['id'] = $_GET['id'];
		//$_SESSION['mail'] = true;
	}
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
					$hid = hash('md5', $salt.$_POST['id']);//generating hash using md5 algorithm
					$hemail = hash('md5', $salt.$_POST['email']);
					send_mail($hid);
					//$_SESSION['success'] = "Your id is ".$hid."The password reset link has been sent to your registered email. Kindly check the verification link to reset your password.";
$_SESSION['success'] = "The password reset link has been sent to your registered email. Kindly check the verification link to reset your password.";
					header('Location: forgot_password.php');
					//header('Location: send_password_mail.php?id='.$huid.'&email='.$hemail);
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

				$hid = hash('md5', $salt.$_POST['id']);//generating hash using md5 algorithm
				$hemail = hash('md5', $salt.$_POST['email']);
				send_mail($hid);
//$_SESSION['success'] = "Your id is ".$hid."The password reset link has been sent to your registered email. Kindly check the verification link to reset your password.";
$_SESSION['success'] = "The password reset link has been sent to your registered email. Kindly check the verification link to reset your password.";
					header('Location: forgot_password.php');
				header('Location: forgot_password.php');
				//header('Location: send_password_mail.php?id='.$haid.'&email='.$hemail);
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