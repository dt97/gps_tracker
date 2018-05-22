<?php
require_once "pdo_skp.php";
session_start();
if (!isset($_SESSION['id']) || $_SESSION['type']!==0) {//for user i.e. user can't acces this page
    die('ACCESS DENIED');
}	
if ( isset($_POST['cancel']) ) {
    header('Location: gps_info.php?u_id='.$_REQUEST['u_id']);
    return;
}
$table = $_GET['u_id']."_account";
$sql = "SELECT * FROM $table WHERE u_tstamp= :dd";
$stmt1 = $pdo->prepare($sql);
$stmt1->execute(array(':dd' => $_GET['u_tstamp']));
$row1 = $stmt1->fetch(PDO::FETCH_ASSOC);
$stmt2 = $pdo->prepare("SELECT * FROM users where u_id = :xyz");
$stmt2->execute(array(":xyz" => $_GET['u_id']));
$row2 = $stmt2->fetch(PDO::FETCH_ASSOC);
$uid = htmlentities($row2['u_id']);
$utstamp = htmlentities($row1['u_tstamp']);
$name = htmlentities($row2['u_name']);
if ( $row2 === false ) {
    $_SESSION['failure']= "No such user exists!";
    header('Location: gps_info.php?u_id='.$_REQUEST['u_id']);
    return;
}
if ( $row1 === false ) {
    $_SESSION['failure']= "No such user time stamp exists for current user!";
    header('Location: gps_info.php?u_id='.$_REQUEST['u_id']);
    return;
}
if ( isset($_POST['delete']) && isset($_POST['uid']) && isset($_POST['utstamp']) ) {
    $table = $_POST['uid']."_account";
    $sql = "DELETE FROM $table WHERE u_tstamp= :dd";
    $stmt1 = $pdo->prepare($sql);
    $stmt1->execute(array(':dd' => $_POST['utstamp']));
    $stmt2 = $pdo->prepare("SELECT * FROM users where u_id = :xyz");
    $stmt2->execute(array(":xyz" => $_POST['uid']));
    $row2 = $stmt2->fetch(PDO::FETCH_ASSOC);
    if ( $row2 === false ) {
        $_SESSION['failure'] = 'Invalid User ID';
        header('Location: gps_info.php?u_id='.$_REQUEST['u_id']);
        return;
    }
    else
    {
        $_SESSION['success'] = 'Record of user '.$_POST["name"].' with ID '.$_POST["uid"].' is deleted for timestamp '.$_POST["utstamp"];
        header('Location: gps_info.php?u_id='.$_REQUEST['u_id']);
        return;
    }
}
// Guardian: Make sure that u_id is present
if (!isset($_GET['u_id']) || !isset($_GET['u_tstamp'])) {
    $_SESSION['failure'] = 'Invalid User ID or invalid user timestamp';
    header('Location: gps_info.php?u_id='.$_REQUEST['u_id']);
    return;
}
?>
<html>
<head>
<title>Deleting User Timestamp Entry</title>
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
<h4> Delete user entry for user <?= $name ?> with ID <?= $uid ?> and for time stamp <?= $utstamp ?></h4>
<p>Confirm: Deleting time stamp <?= $utstamp ?> for user <?= $name ?> with ID <?= $uid ?></p>
<form method="post">
<input type="hidden" name="uid" size="40" value="<?= $uid ?>"/>
<input type="hidden" name="name" size="40" value="<?= $name ?>"/>
<input type="hidden" name="utstamp" size="40" value="<?= $utstamp ?>"/>
<input type="submit" value="Delete" name="delete">
<input type="submit" value="Cancel" name="cancel">
</form>
</div>
</body>