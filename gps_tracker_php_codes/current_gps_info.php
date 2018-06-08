<?php
	require_once "pdo_skp.php";
	session_start();
	static $max_diff = 1000000;//max difference of 10 minutes is allowed between current time and latest time recorded in gps_tracker database
	if(isset($_SESSION['id']))
	{
		$u_id = $_REQUEST['u_id'];
		//echo "$u_id<br>";
		$stmt1 = $pdo->prepare("SELECT * FROM users where u_id = :u_id");
		$stmt1->execute(array('u_id' => $u_id));
		$row1 = $stmt1->fetch(PDO::FETCH_ASSOC);
		/*if($row1)
		{
			echo "1<br>";
		}
		else
		{
			echo "0<br>";
		}*/
		if($row1)
		{
			$table = $u_id.'_account';
			$stmt2 = $pdo->prepare("SELECT * FROM $table ORDER BY u_tstamp DESC LIMIT 1");//to get the last time stamp recorded for the given user in database
			$stmt2->execute();
			$row2 = $stmt2->fetch(PDO::FETCH_ASSOC);
			if($row2)
			{
				$current_timestamp = date('Y-m-d G:i:s');
				$latest_timestamp = $row2['u_tstamp'];
				$t_diff = abs($current_timestamp-$latest_timestamp);
				if($t_diff<=$max_diff)
				{
					$_SESSION['success'] = "Successfully retrieved your current location details";
					$url = $row2['u_loc'];
					header('Location: '.$url);
					return;
				}
			}
			else
			{
				echo "Failure in database. We'll get back to you soon. Apologies for the inconveniences<br>";
			}
		}
		else
		{
			$_SESSION['failure'] = "No such user id exists in users table for accessing current location. Please try again";
			header('Location: gps_info.php?u_id='.$_REQUEST['u_id']);
			return;
		}
	}
	else
	{
		session_destroy();
		die('ACCESS DENIED');
	} 
?>