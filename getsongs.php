<?php
require_once("include/config.inc.php");

if (isset($_GET['screen']))
{
	$mysqli = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
	$screen = $mysqli->real_escape_string($_GET['screen']);
	
	$tracks = array();
	
	$query	= "SELECT * FROM queue WHERE screen = '$screen' ORDER BY votes DESC, timestamp ASC";
	$result	= $mysqli->query($query);
	
	if ($result->num_rows > 0)
	{
		while ($row = $result->fetch_array(MYSQLI_ASSOC))
		{
			$tracks[] = array
			(
				"id"		=> $row['id'],
				"artist"	=> $row['artist'],
				"track"		=> $row['track'],
				"thumbnail"	=> $row['thumbnail'],
				"votes"		=> $row['votes']
			);
		}
		
		$output = json_encode($tracks);
		echo $output;
	}
	else
	{
		echo "SCREENFAIL";
	}
}
else
{
	echo "SCREENFAIL";
}