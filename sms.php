<?php
require_once("include/config.inc.php");
require_once("messagebird-rest-api/autoload.php");

define("MESSAGEBIRD_KEY", "live_sgORFbRAkHBJnUcTrGAtzlQCi");

$mysqli = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_DATABASE);

$origin = $mysqli->real_escape_string($_GET['originator']);
$recipient = $_GET['recipient'];

$body = $_GET['body'];

$body_split = explode(" ", $body);
$body_split[0] = strtolower($body_split[0]);
$body_split[1] = strtolower($mysqli->real_escape_string($body_split[1]));

if ($body_split[0] == "register")
{
	$screen = null;
	
	$query = "SELECT * FROM screens WHERE pin = '" . $body_split[1] . "'";
	$result = $mysqli->query($query);
	
	$MessageBird = new \MessageBird\Client(MESSAGEBIRD_KEY);
		
	$Message             = new \MessageBird\Objects\Message();
	$Message->originator = 'YourMusicTV';
	$Message->recipients = array($_GET['originator']);
	
	if ($result->num_rows > 0)
	{
		$row = $result->fetch_array(MYSQLI_ASSOC);
		$querycheck = "SELECT * FROM mobile WHERE client = '$origin'";
		$resultcheck = $mysqli->query($querycheck);
		if ($resultcheck->num_rows > 0)
		{
			$query2 = "UPDATE mobile SET pin = '" . $row['pin'] . "' WHERE client = '$origin'"; 
		}
		else
		{
			$query2 = "INSERT INTO mobile VALUES(NULL, '$origin', '" . $row['pin'] . "')";
		}
		$mysqli->query($query2);
		$Message->body = "Your phone has now been registered to control the " . $row['name'] . " TV, at " . $row['location'] . " :)";
	}
	else
	{
		$Message->body = 'Unfortunately the key you sent was incorrect :(';
	}
	$MessageResult = $MessageBird->messages->create($Message);
}

http_response_code(200);