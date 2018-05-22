<?php
require_once "pdo_skp.php";
session_start();
$salt = 'XyZzy12*_';
if ( isset($_POST['id']) && isset($_POST['pw']) ) {
	unset($_SESSION['id']);  // Logout current user
    if ( strlen($_POST['id']) < 1 || strlen($_POST['pw']) < 1 ) {
        $_SESSION['failure'] = "User id and password are required";
		header( 'Location: index2.php' ) ;
        return;
    } else {
		$stmt1 = $pdo->prepare("SELECT a_id, a_pw FROM admin where a_id = :id");
		$stmt1->execute(array(':id' => $_POST['id']));
		$row1 = $stmt1->fetch(PDO::FETCH_ASSOC);
		if ( $row1 === false ) {
			$stmt2 = $pdo->prepare("SELECT u_id, u_pw FROM users where u_id = :id");
			$stmt2->execute(array(':id' => $_POST['id']));
			$row2 = $stmt2->fetch(PDO::FETCH_ASSOC);
			if ( $row2 === false ){
				$_SESSION['failure']="No such user ID";
				header( 'Location: index2.php' ) ;
				return;
			}
			else {
				//$check = $POST['pw'];
				$check = hash('md5', $salt.$_POST['pw']);    //convert pw to hash to check with hash value of pw in db
				$_SESSION['check']=$check;
				if ( $check == $row2['u_pw']) {
					// Redirect the browser to view.php
					$_SESSION['id'] = $_POST['id'];
					$_SESSION['type'] = 1;            // type 0 for admin, type 1 for users
					$_SESSION['success'] = "User login successful";
					header( 'Location: index2.php' ) ;
					error_log("Login success ".$_SESSION['id']);
					return;
				} else{
					$_SESSION['failure'] = "Incorrect password.";
					header( 'Location: index2.php' ) ;
					error_log("Login fail ".$_SESSION['id']." $check");
					return;
				} 
			}
		}
		else {
			$check = hash('md5', $salt.$_POST['pw']);    //convert pw to hash to check with hash value of pw in db
			$_SESSION['check']=$check;
			if($check==$row1['a_pw'])
			{
				$_SESSION['type'] = 0;//0 for admin, 1 for users
				$_SESSION['success'] = "admin login successful";
				//echo "Login Success<br>";
			}
			else
			{
				$_SESSION['failure'] = "admin login failure";
				header('Location: index2.php');
				//echo "Login Failure<br>";
			}
			/*if ( $check == $row1['a_pw']) {
				// Redirect the browser to view.php
				$_SESSION['id'] = $_POST['id'];
				$_SESSION['type'] = 0;            // type 0 for admin, type 1 for teachers
				header( 'Location: view.php' ) ;
				error_log("Login success ".$_SESSION['id']);
				return;
			} else{
				$_SESSION['failure'] = "Incorrect password.";
				header( 'Location: index.php' ) ;
				error_log("Login fail ".$_SESSION['id']." $check");
				return;
			}*/ 
		
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<title>GPS Tracker Login Page</title>
<link rel="stylesheet" href="css/bootstrap.min.css">
<link rel="stylesheet" href="css/detail.css">
</head>
<body>
<div class="container">
<h1>GPS Tracker</h1><hr>
<h2>Please Log In</h2>
<?php
if ( isset($_SESSION['failure']) ) {
    echo('<p style="color: red;">'.htmlentities($_SESSION['failure'])."</p>\n");
	echo $_SESSION['check'];
	echo $_SESSION['type'];
    unset($_SESSION['failure']);
}
else if( isset($_SESSION['success']) ) {
    echo('<p style="color: green;">'.htmlentities($_SESSION['success'])."</p>\n");
	echo $_SESSION['check'];
	echo $_SESSION['type'];
    unset($_SESSION['success']);
}
?>
<form method="POST" action="index2.php">
<label for="user_id">User ID</label>
<input type="text" name="id" id="user_id"><br/>
<label for="pass">Password</label>
<input type="password" name="pw" id="pass"><br/>
<br>
<input type="submit" value="Log In">
</form>
</div>
</body>
</html>