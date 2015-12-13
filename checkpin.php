<?php
require_once("include/config.inc.php");
if (isset($_GET['screen']) && isset($_GET['pin']))
{
	$mysqli	= new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
	$screen	= $mysqli->real_escape_string($_GET['screen']);
	$pin	= $mysqli->real_escape_string($_GET['pin']);
	
	$query	= "SELECT * FROM screens WHERE id = '$screen'";
	$result	= $mysqli->query($query);
	
	if ($result->num_rows > 0)
	{
		$row = $result->fetch_array(MYSQLI_ASSOC);
		if ($row['pin'] == $pin)
		{
			echo "SUCCESS";
		}
		else
		{
			echo "PINFAIL";
		}
	}
	else
	{
		echo "SCREENFAIL";
	}
}