<?php
require_once("include/config.inc.php");
require_once("messagebird-rest-api/autoload.php");

define("MESSAGEBIRD_KEY", "live_sgORFbRAkHBJnUcTrGAtzlQCi");

$mysqli = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_DATABASE);

$origin = $mysqli->real_escape_string($_GET['originator']);
$recipient = $_GET['recipient'];

$body = $_GET['body'];

$screens_json = file_get_contents("http://projectnadia.windowshelpdesk.co.uk/Server/getscreens.php?showpin=1");
$screens = json_decode($screens_json, true);

$body_split = explode(" ", $body);
$body_split[0] = strtolower($body_split[0]);

if ($body_split[0] == "register")
{
	$screen = null;

	foreach ($screens as $s)
	{
		if ($body_split[1] == $s['pin'])
		{
			$screen = $s;
		}
	}
	
	$MessageBird = new \MessageBird\Client(MESSAGEBIRD_KEY);
	
	$Message             = new \MessageBird\Objects\Message();
	$Message->originator = 'YourMusicTV';
	$Message->recipients = array($_GET['originator']);
	
	if ($screen == null)
	{
		$Message->body = 'Unfortunately the key you sent was incorrect :(';
	}
	else
	{
		$query2 = "INSERT INTO mobile VALUES(NULL, '$origin', '" . $s['pin'] . "')";
		$mysqli->query($query2);
		$Message->body = "Your phone has now been registered to control the " . $s['name'] . " TV, at " . $s['location'] . " :)";
	}
	$MessageResult = $MessageBird->messages->create($Message);
	
}


http_response_code(200);