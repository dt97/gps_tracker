<?php
require_once "pdo_skp.php";
session_start();
static $u_id = "";//to store u_id of registered user
static $lat = "";//to store latitude value
static $lon = "";//to store longitude value
static $url = "https://maps.google.com/maps?q="; //url to be generated to link to google maps page
foreach ($_REQUEST as $key => $value)
{
	if($key == "u_id")
	{
		$u_id = $value;
	}
	else if($key == "latitude")//when crfid is sent from node mcu to this php code
	{
		$lat = $value;
	}
	else if($key == "longitude")
	{
		$lon = $value;
	}
}
if($u_id!="" && $lat!="" && $lon!="")
{
	$latdeg = substr($lat, 0, 2);
	$latmin = substr($lat, 2);
	$londeg = substr($lon, 0, 3);
	$lonmin = substr($lon, 3);
	$url = $url.$latdeg." ".$latmin.",".$londeg." ".$lonmin;
	echo "Your gps tracker url is $url for user $u_id<br>";
	echo "<a href='$url'>To know your exact location, please click here</a><br>";
	$stmt1 = $pdo->prepare("SELECT * FROM users WHERE u_id=:u_id");
	$stmt1->execute(array(":u_id" => $u_id));
	$row1 = $stmt1->fetch(PDO::FETCH_ASSOC);
	if($row1===false)
	{
		echo "No such user exists in our database kindly contact your gps_tracker seller or try again<br>";
	}
	else
	{
		$table = $u_id.'_account';
		$stmt2 = $pdo->prepare("SELECT * FROM $table ORDER BY u_tstamp DESC LIMIT 1");//to get the last time stamp recorded for the given user in database
		$stmt2->execute();
		$row2 = $stmt2->fetch(PDO::FETCH_ASSOC);
		if($row2===false)
		{
			echo "Failure in database. We'll get back to you soon. Apologies for the inconveniences<br>";
		}
		else
		{
			$current_timestamp = date('Y-m-d G:i:s');
			$latest_timestamp = $row2['u_tstamp'];
			if($current_timestamp>$latest_timestamp)
			{
				$stmt3 = $pdo->prepare("INSERT INTO $table (u_tstamp, u_loc) VALUES (:u_tstamp, :u_loc)");
				if($stmt3->execute(array('u_tstamp' => $current_timestamp, 'u_loc' => $url)))//query success
				{
					echo "Successfully entered new location entry into $table in gps tracker database<br>";
				}
				else
				{
					echo "Sql error. Please try again<br>";
				}
			}
		}
	}
}
else
{
	echo "Please enter valid latitude and longitude coordinates<br>";
}
?>