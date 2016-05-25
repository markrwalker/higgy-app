<?php
	ini_set('memory_limit', '256M');
	ini_set('max_execution_time', 300);
	ini_set('display_errors',1);
	error_reporting(E_ALL);
	require_once('mobile/config.php');
	global $year_id;
	$year_id = 4;

	$games = array();
	$sql1 = "SELECT * FROM `game` WHERE `year_id` = $year_id AND `is_complete` = 0";
	$result1 = mysql_query($sql1);
	while ($row = mysql_fetch_assoc($result1)) {
		$games[] = $row;
	}
	//echo '<pre>'.print_r($games,1).'</pre>'; die();

	foreach ($games as $game) {
		$winner = rand(1,2);
		$loser = $winner == 2 ? 1 : 2;
		$w = rand(1,6) == 6 ? rand(12,17) : 12;
		$l = rand(0,10);
		if ($w > 12) {
			$l = $w - 2;
		}
		if ($game['team1_id'] == 999) {
			$winner = 2;
			$loser = 1;
			$w = 12;
			$l = 0;
		} else if ($game['team2_id'] == 999) {
			$winner = 1;
			$loser = 2;
			$w = 12;
			$l = 0;
		}
		mysql_query("UPDATE `score` SET `score` = $w WHERE `id` = ".$game['team'.$winner.'_score_id']);
		mysql_query("UPDATE `score` SET `score` = $l WHERE `id` = ".$game['team'.$loser.'_score_id']);
		if (mysql_query("UPDATE `game` SET `is_complete` = 1 WHERE `id` = ".$game['id'])) {
			echo 'Updated '.$game['id'].'<br>';
		} else {
			echo 'Failed '.$game['id'].'<br>';
		}
	}

