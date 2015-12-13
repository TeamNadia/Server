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

$MessageBird = new \MessageBird\Client(MESSAGEBIRD_KEY);
		
$Message             = new \MessageBird\Objects\Message();
$Message->originator = 'YourMusicTV';
$Message->recipients = array($_GET['originator']);

if ($body_split[0] == "register")
{
	$screen = null;
	
	$query = "SELECT * FROM screens WHERE pin = '" . $body_split[1] . "'";
	$result = $mysqli->query($query);
	
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
}
elseif ($body_split[0] == "killswitch")
{
	$screen = $body_split[1];
	$pos = $body_switch[2] - 1;
	
	$songs_json = file_get_contents("http://projectnadia.windowshelpdesk.co.uk/Server/getsongs.php?screen=$screen");
	$songs = json_decode($songs_json, true);
	
	$idtoupdate = $songs[$pos]['id'];
	$query = "UPDATE queue SET url = 'QH2-TGUlwu4' WHERE id = '$idtoupdate'";
	$mysqli->query($query);
	
	$Message->body = 'Killswitch sent to ' . $songs[$pos]['track'];
}
else
{
	$query = "SELECT * FROM mobile WHERE client = '$origin'";
	$result = $mysqli->query($query);
	if ($result->num_rows > 0)
	{
		$row = $result->fetch_array(MYSQLI_ASSOC);
		$screens = array();
		$query2 = "SELECT * FROM screens";
		$result2 = $mysqli->query($query2);
		while ($row2 = $result2->fetch_array(MYSQLI_ASSOC))
		{
			$screens[] = $row2;
		}
		$screen = null;
		
		foreach ($screens as $s)
		{
			if ($s['pin'] == $row['pin'])
			{
				$screen = $s;
			}
		}
		
		$artist = "";
		$i = 0;
		for ($i = 1; $i < count($body_split); $i++)
		{
			$artist.= " " . $body_split[$i];
		}
		
		$songresult = file_get_contents("http://projectnadia.windowshelpdesk.co.uk/Server/submitsong.php?screen=" . $s['id']
			. "&pin=" . $s['pin'] . "&artist=" . $artist);
		
		if ($songresult == "SUCCESS")
		{
			$Message->body = 'Your request was successful! Thank you! :)';
		}
		else
		{
			$Message->body = 'Unfortunately, that request failed. :( Please try again. If issues persist please contact us.';
		}
	}
	else
	{
		$Message->body = 'Sorry, you must register with a pin before you can use this service. Simply test register followed by the TV pin to register. Thank you!';
	}
}

$MessageResult = $MessageBird->messages->create($Message);

http_response_code(200);