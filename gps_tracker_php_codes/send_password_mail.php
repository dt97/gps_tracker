<?php
require_once "pdo_skp.php";
session_start();
//$salt = 'XyZzy12*_';
//get a mail yourself about successful redirection of password reset link
$_SESSION['id'] = $_GET['id'];
$message = 'http://skaipal.in/gps_tracker/reset_password.php?id='.$_GET['id'];
$email_send = $_GET['email'];
$email_from = 'skaipal.in';//<== update the email address
$email_subject = "Password reset link for gps tracker website";
$email_body = "You have received a new message regarding password reset link from $email_send \n".
	"Here is the password reset url:\n $message \n";
$to = "dt.kanha@gmail.com";//<== update the email address
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
/*else
{
	unset($_SESSION['mail']);
}*/
?>
<!DOCTYPE html>
<html>
<head>
<title>Send password mail</title>
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
</div>
</body>
</html>