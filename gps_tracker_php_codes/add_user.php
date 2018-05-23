<?php
require_once "pdo_skp.php";
session_start();

$salt = 'XyZzy12*_';

//echo $_SESSION['type'];
if(!isset($_SESSION['id']) || $_SESSION['type']!==0 ) { //if its admin type = 0 
    die('ACCESS DENIED');
}
if ( isset($_POST['cancel']) ) {
    header('Location: user_info.php');
    return;
}

if ( isset($_POST['u_id']) && isset($_POST['u_name']) && isset($_POST['u_pw']) ){
	if(strlen($_POST['u_id']) < 1 || strlen($_POST['u_name']) < 1 ||strlen($_POST['u_pw']) < 1 ){
		$_SESSION['failure']='All fields are required';
		header( 'Location: add_user.php' ) ;
		return;
	}
	else
	{
		$stmt = $pdo->prepare("SELECT * FROM users where u_id = :u_id");//to check if the u_id already exits in users database
		$stmt->execute(array(":u_id" => $_POST['u_id']));
		$already_exist = $stmt->fetch(PDO::FETCH_ASSOC);
		if ($already_exist) {
			$_SESSION['failure'] = "User with this ID already exists! Chose another id!!";
			header('Location: add_user.php') ;
			return;
		}
		else{
			$hashed_pw = hash('md5', $salt.$_POST['u_pw']);	//store u_pw in hashed format 
			$_SESSION['check']=$hashed_pw;
			$email = $_POST['u_email'];
			if (!filter_var($email, FILTER_VALIDATE_EMAIL)) 
			{
  				$_SESSION['failure'] = "Invalid email format";
  				header('Location: add_user.php'); 
			}
			else
			{
				$stmt = $pdo->prepare('INSERT INTO users (u_id, u_name, u_pw, u_email) VALUES ( :u_id, :u_name, :u_pw, :u_email)');
				$stmt->execute(array(
					':u_id' => $_POST['u_id'],
					':u_name' => $_POST['u_name'],
					':u_pw' => $hashed_pw,
					':u_email' => $_POST['u_email']));
				$table  = $_POST['u_id']."_account";
				$sql= "CREATE TABLE $table (
  							u_tstamp TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  							u_loc VARCHAR(255),
  							PRIMARY KEY(u_tstamp)
							) ENGINE = InnoDB;";
				$creat=$pdo->exec($sql);
				$_SESSION['success'] = 'New user added';
				header('Location: user_info.php');
			}
			return;
		}
	}
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Adding new teacher info</title>
<link rel="stylesheet" href="css/bootstrap.min.css">
<link rel="stylesheet" href="css/detail.css">
</head>
<body>
<div class="container">
<h1>GPS Tracking System</h1><hr>
<h2> Welcome 
<?php
if(isset($_SESSION['id']) && $_SESSION['type']===0){             //ADMIN
	$stmt = $pdo->prepare("SELECT a_name FROM admin WHERE a_id=:id");
	$stmt->execute(array(
						':id' => $_SESSION['id']));
	$row = $stmt->fetch(PDO::FETCH_ASSOC);
	echo(" Admin ".$row['a_name']."</h2>");
}

if ( isset($_SESSION['failure']) ) {
        echo('<p style="color: red;">'.htmlentities($_SESSION['failure'])."</p>\n");
        unset($_SESSION['failure']);
    }
?>
<h3>Add information about the new user</h3>
<form method="post">
<p>User ID:
<input type="text" name="u_id" size="40"/></p>
<p>Name:
<input type="text" name="u_name" size="40"/></p>
<p>Password:
<input type="password" name="u_pw" size="15"/></p>
<p>Email:
<input type="email" name="u_email" size="50"/></p>
<input type="submit" name='add' value="Add">
<input type="submit" name="cancel" value="Cancel">
</form>
<p>
</div>
</body>
</html>