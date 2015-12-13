<?php
require_once("include/config.inc.php");
set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__) . '/google-api-php-client/src');
require_once('Google/autoload.php');
require_once('Google/Client.php');
require_once('Google/Service/YouTube.php');

if (isset($_GET['screen']) && isset($_GET['pin']) && isset($_GET['artist']))
{
	// Before anything, check that the screen pin is correct
	$mysqli	= new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
	$screen	= $mysqli->real_escape_string($_GET['screen']);
	$pin	= $mysqli->real_escape_string($_GET['pin']);
	
	$screenquery = "SELECT * FROM screens WHERE id = $screen";
	$screenresult = $mysqli->query($screenquery);
	if ($screenresult->num_rows > 0)
	{
		$screenrow = $screenresult->fetch_array(MYSQLI_ASSOC);
		if ($screenrow['pin'] == $pin)
		{
			// Screen pin is ok, so go ahead and actually do some shit
			$artist	= $mysqli->real_escape_string($_GET['artist']);
			$track = "";
			if (isset($_GET['track']))
				$track = $mysqli->real_escape_string($_GET['track']);
			
			$searchquery = $artist;
			if ($track != "")
				$searchquery .= " - " . $track;
				
			// Connect up to YouTube	
			$googleclient = new Google_Client();
			$googleclient->setDeveloperKey(YOUTUBE_KEY);
			$youtube = new Google_Service_YouTube($googleclient);
			
			$searchresponse = $youtube->search->listSearch('id,snippet', array(
				'q' => $searchquery,
				'maxResults' => 5
			));
			
			$videos = array();
			foreach($searchresponse['items'] as $searchresult)
			{
				if ($searchresult['id']['kind'] == 'youtube#video')
				{
					$splitTitle = explode(" - ", $searchresult['snippet']['title']);
					$thisTrack = $splitTitle[1];
					if (count($splitTitle > 2))
						for($i = 3; $i < count($splitTitle); $i++)
							$thisTrack .= " - " . $splitTitle[$i];
					
					$videos[] = array(
						"url"		=> $searchresult['id']['videoId'],
						"artist" 	=> $splitTitle[0],
						"track" 	=> $thisTrack,
						"thumbnail"	=> $searchresult['snippet']['thumbnails']['default']['url']
					);
				}
			}
			
			$url = "";
			$url = $mysqli->real_escape_string($videos[0]['url']);
			
			if ($url == "")
			{
				echo "URLFAIL";
			}
			else
			{
				$query	= "SELECT * FROM queue WHERE url = '$url'";
				$result	= $mysqli->query($query);
				if ($result->num_rows > 0)
				{
					$row = $result->fetch_array(MYSQLI_ASSOC);
					$votes = (int) $row['votes'];
					$votes++;
					
					$id = $row['id'];
					
					$query2 = "UPDATE queue SET votes = $votes WHERE id = $id";
					$mysqli->query($query2);				
				}
				else
				{
					$artist = $mysqli->real_escape_string($videos[0]['artist']);
					$track	= $mysqli->real_escape_string($videos[0]['track']);
					$thumbnail = $mysqli->real_escape_string($videos[0]['thumbnail']);
					$query2 = "INSERT INTO queue VALUES (NULL, $screen, '$artist', '$track', '$thumbnail', '$url', 1, NULL)";
					$mysqli->query($query2);
				}
				echo "SUCCESS";
			}			
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
	echo "NOTHINGFAIL";
}