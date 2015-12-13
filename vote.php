<?php
require_once("include/config.inc.php");
if (isset($_GET['screen']) && isset($_GET['pin']) && isset($_GET['id']) && isset($_GET['vote']))
{
	if ($_GET['vote'] == "1" || $_GET['vote'] == "-1")
	{
		$mysqli	= new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
		$screen	= $mysqli->real_escape_string($_GET['screen']);
		$pin	= $mysqli->real_escape_string($_GET['pin']);
		$id		= $mysqli->real_escape_string($_GET['id']);
		$vote	= $mysqli->real_escape_string($_GET['vote']);
		$query	= "SELECT * FROM screens WHERE id = '$screen'";
		$result = $mysqli->query($query);
		if ($result->num_rows > 0)
		{
			$screenrow = $result->fetch_array(MYSQLI_ASSOC);
			if ($screenrow['pin'] == $pin)
			{
				$query2 	= "UPDATE queue SET votes = votes + $vote WHERE screen = '$screen' AND id = '$id'";
				$mysqli->query($query2);
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
	else
	{
		echo "VOTEFAIL";
	}
}
else
{
	echo "NOTHINGFAIL";
}