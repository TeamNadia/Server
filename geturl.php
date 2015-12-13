<?php
require_once("include/config.inc.php");

if (isset($_GET['screen']) && isset($_GET['id']))
{
	$mysqli	= new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
	$screen	= $mysqli->real_escape_string($_GET['screen']);
	$songid	= $mysqli->real_escape_string($_GET['id']);
	
	$query	= "SELECT * FROM queue WHERE id = '$songid' AND screen = '$screen'";
	$result	= $mysqli->query($query);
	
	if ($result->num_rows > 0)
	{
		$row = $result->fetch_array(MYSQLI_ASSOC);
		$url = $row['url'];
		echo $url;
		$query2 = "DELETE FROM queue WHERE id = '$songid'";
		$mysqli->query($query2);
	}
	else
	{
		echo "EVERYTHINGFAIL";
	}
}
else
{
	echo "NOTHINGFAIL";
}