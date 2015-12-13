<?php
require("include/config.inc.php");
$mysqli = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_DATABASE);

$screens = array();

$query	= "SELECT * FROM screens";
$result	= $mysqli->query($query);

while ($row = $result->fetch_array(MYSQLI_ASSOC))
{
	$screenData = array
	(
		"id"		=> $row['id'],
		"name"		=> $row['name'],
		"location"	=> $row['location']
	);
	
	if (isset($_GET['showpin']) && $_GET['showpin'] == "1")
	{
		$screenData['pin'] = $row['pin'];
	}
	
	$screens[] = $screenData;
}

$output = json_encode($screens, JSON_NUMERIC_CHECK);

echo $output;