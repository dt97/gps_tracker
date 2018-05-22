<?php
require_once "pdo_skp.php";
session_start();
if (!isset($_SESSION['id']) || $_SESSION['type']!==0) {//for user i.e. user can't acces this page
    die('ACCESS DENIED');
}	
if ( isset($_POST['cancel']) ) {
    header('Location: user_info.php');
    return;
}
if ( isset($_POST['delete']) && isset($_POST['u_id']) ) {
    $sql = "DELETE FROM users WHERE u_id= :dd";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(':dd' => $_POST['u_id']));
    $table = $_POST['u_id']."_account";
    $sql = "DROP TABLE $table";
    $del = $pdo->exec($sql);
    $_SESSION['success'] = 'Record of user '.$_POST['u_name'].' with ID '.$_POST['u_id'].' is deleted';
    header( 'Location: user_info.php' ) ;
    return;
}
// Guardian: Make sure that u_id is present
if (!isset($_GET['u_id'])) {
  $_SESSION['failure'] = 'Invalid User ID';
  header('Location: user_info.php');
  return;
}
$stmt = $pdo->prepare("SELECT u_name, u_id FROM users where u_id = :xyz");
$stmt->execute(array(":xyz" => $_GET['u_id']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ( $row === false ) {
	$_SESSION['failure']= "No such user exists!";
    header( 'Location: user_info.php' ) ;
    return;
}

?>
<html>
<head>
<title>Deleting teacher entry</title>
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
<h4> Delete user entry</h4>
<p>Confirm: Deleting <?= htmlentities($row['u_name']) ?></p>
<form method="post">
<input type="hidden" name="u_name" value="<?= $row['u_name'] ?>"> 
<input type="hidden" name="u_id" value="<?= $row['u_id'] ?>"> 
<input type="submit" value="Delete" name="delete">
<input type="submit" value="Cancel" name="cancel">
</form>
</div>
</body>