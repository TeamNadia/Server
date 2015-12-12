<?php
include_once("include/config.inc.php");
set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__) . '/google-api-php-client/src');

if (isset($_GET['screen']) && isset($_GET['pin']) && isset($_GET['artist']))
{
	// Connect up to YouTube!
	
	$mysqli	= new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
	$screen	= $mysqli->real_escape_string($_GET['screen']);
	$pin	= $mysqli->real_escape_string($_GET['pin']);
	$url	= $mysqli->real_escape_string($_GET['url']);
	
	$query	= "SELECT * FROM queue WHERE url = '$url'";
	$result	= $mysqli->query($query);
	if ($result->num_rows > 0)
	{
		$row = $result->fetch_array(MYSQLI_ASSOC);
		$upvotes = (int) $row['upvotes'];
		$upvotes++;
		
		$id = $row['id'];
		
		$query2 = "UPDATE queue SET upvotes = $upvotes WHERE id = $id";
		$mysqli->query($query2);
		
		echo "SUCCESS";
	}
	else
	{
		
		
	}
}