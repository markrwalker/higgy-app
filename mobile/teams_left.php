<?php
include("config.php");

$team_data = array();
$sql1 = "SELECT * FROM team";
$result1 = mysql_query($sql1);
while ($row = mysql_fetch_assoc($result1)) {
	$team_data[] = $row;
}

foreach ($team_data as $team) {
	//echo '<pre>'.print_r($team,1).'</pre>';
	$my_name = $team['name'];
	$opponents_data = array();
	$sql2 = "SELECT * FROM game_scores WHERE (team1 = '$my_name' OR team2 = '$my_name')";
		$opponents_data[] = $row;
	}

	echo $team['name'].'<br /><pre>'.print_r($opponents_data,1),'<pre>';
}