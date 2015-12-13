<?php
require("include/config.inc.php");
$mysqli = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_DATABASE);

$screens = array();

$query	= "SELECT * FROM screens";
$result	= $mysqli->query($query);

while ($row = $result->fetch_array(MYSQLI_ASSOC))
{
	$screens[$row['id']] = array
	(
		"id"		=> $row['id'],
		"name"		=> $row['name'],
		"location"	=> $row['location']
	);
}

$output = json_encode($screens);

echo $output;