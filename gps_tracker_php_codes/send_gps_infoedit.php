<?php
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
if($lat!="" && $lon!="")
{
	$latdeg = substr($lat, 0, 2);
	$latmin = substr($lat, 2);
	$londeg = substr($lon, 0, 3);
	$lonmin = substr($lon, 3);
	$url = $url.$latdeg." ".$latmin.",".$londeg." ".$lonmin;
	echo "Your gps tracker url is $url for user $u_id<br>";
	echo "<a href='$url'>To know your exact location, please click here</a><br>";
}
else
{
	echo "Please enter valid latitude and longitude coordinates<br>";
}
?>