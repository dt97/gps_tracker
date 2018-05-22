<?php
require_once "pdo_skp.php";
session_start();
if ( ! isset($_SESSION['id'])) {
    die('ACCESS DENIED');
}
//$_SESSION['failure'] = "You are not an admin. Can't change your gps info";
$_SESSION['user']=$_GET['u_id'];
// If the user requested logout go back to main.php
if ( isset($_POST['cancel']) ) {
	unset($_SESSION['user']);
	if($_SESSION['type']===0)//if admin
	{
		header('Location: user_info.php');
	}
	else//if user
	{
		header('Location: main.php');
	}
    return;
}
else if(isset($_POST['save']))
{
	header('Location: gps_info.php?u_id='.$_REQUEST['u_id']);//to redirect to gps_info.php after admin edits the location details for the user
	return;
}
/*if(isset($_POST['save']))
{
	if(isset($_SESSION['type']===0))//if its admin
	{
		header('Location: gps_info.php');
	}
	else
	{
		$_SESSION['failure'] = "You are not an admin. Can't change your gps info";

	}
}*/
?>
<!DOCTYPE html>
<html>
<head>
<title>GPS Information</title>
<link rel="stylesheet" type="text/css" href="css/bootstrap.min.css"/>
<link rel="stylesheet" href="css/detail.css"/>
<script type="text/javascript" src="jquery-1.11.1.min.js"></script>
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
	$row = $stmt->fetch(PDO::FETCH_ASSOC);
	echo(" Admin ".$row['a_name']);
}
else if( isset($_SESSION['id']) && $_SESSION['type']===1){        //USER
	$stmt = $pdo->prepare("SELECT u_name FROM users WHERE u_id=:id");
	$stmt->execute(array(
						':id' => $_SESSION['id']));
	$row = $stmt->fetch(PDO::FETCH_ASSOC);
	echo(" User ".$row['u_name']);
}
?>
</h2>
<h4> Daily GPS record for user <?=$_SESSION['user']?></h4>
<div id="msg" class="alert">
</div>
<?php
if ( isset($_SESSION['failure']) ) {
        echo('<p style="color: red;">'.htmlentities($_SESSION['failure'])."</p>\n");
        unset($_SESSION['failure']);
    }
	
if ( isset($_SESSION['success']) ) {
        echo('<p style="color: blue;">'.htmlentities($_SESSION['success'])."</p>\n");
        unset($_SESSION['success']);
	}

if(isset($_SESSION['id']))://if id is of user or admin
	$_SESSION['user']=$_GET['u_id'];
	$table = $_GET['u_id']."_account";
	$stmt = $pdo->query("SELECT * FROM $table");
	$count=$stmt->rowCount();
	$arr = Array();
	if( $output = $stmt->fetchAll(PDO::FETCH_ASSOC)):
?>
		<table border="1">
		<thead><tr>
				<th><center>User_Timestamp</center></th>
				<th>&nbsp; User Location &nbsp;</th>
				<?php
				if($_SESSION['type']===0)
				{
					echo("<th>&nbsp;Actions &nbsp;</th>");
				}
				?>
				</tr></thead>

		<?php foreach ( $output as $output ):?>
		<tr data-row-id="<?= $output['u_tstamp']?>">
		<td><?php echo(htmlentities($output['u_tstamp']));?></td>
		<td><center><?php echo(htmlentities($output['u_loc']));?></td>
		<?php
		if($_SESSION['type']===0)//if its admin
		{
			echo ('<td><center><a href="edit_gps_info.php?u_id='.$_SESSION['user'].'&u_tstamp='.$output['u_tstamp'].'">&nbsp;Edit&nbsp;</a> / <a href="delete_gps_info.php?u_id='.$_SESSION['user'].',u_tstamp='.$output['u_tstamp'].'">&nbsp;Delete&nbsp;</a></td>');
			//echo ('<td><center><a href="edit_gps_info.php?u_id='.$output['u_id'].',u_tstamp='.$output['u_tstamp'].'">&nbsp;Edit&nbsp;</a> / <a href="delete_gps_info.php?u_id='.$output['u_id'].',u_tstamp='.$output['u_tstamp'].'">&nbsp;Delete&nbsp;</a></td>');
		}
		?>
		<?php endforeach; 
		?>
		</tr></table>
	
	<?php else :
		echo ("No gps info available currently!");
		endif;
	endif;
	?>
<form action="gps_info.php?u_id=<?=$_GET['u_id']?>" method="POST" >
<br>
<?php
if($_SESSION['type']===0)
{
	echo('<input type="submit" value="Show records available" name="save">');
}
?>
<input type="submit" value="Go back to your account" name="cancel">
</form>
</div>
</body>
</html>

<script type="text/javascript">
$(document).ready(function(){
		$('td.editable-col').on('focusout', function() {
		data = {};
		data['val'] = $(this).text();
		data['id'] = $(this).parent('tr').attr('data-row-id');
		data['index'] = $(this).attr('col-index');
	    if($(this).attr('oldVal') === data['val'])
		return false;
		$.ajax({   
				  
					type: "POST",  
					url: "server.php",  
					cache:false,  
					data: data,
					dataType: "json",				
					success: function(response)  
					{   
						//$("#loading").hide();
						if(!response.error) {
							$("#msg").removeClass('alert-danger');
							$("#msg").addClass('alert-success').html(response.msg);
							$("#msg").fadeIn('slow');
							$("#msg").fadeOut('slow');
						} else {
							$("#msg").removeClass('alert-success');
							$("#msg").addClass('alert-danger').html(response.msg);
							$("#msg").fadeIn('slow');
							$("#msg").fadeOut('slow');
						}
						
						/*setTimeout(function() {
							$('#msg').fadeOut('fast');
							}, 2000); // <-- time in milliseconds*/
					}   
				});
	});
});
</script>