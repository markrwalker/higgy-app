<?php
	require_once('config.php');
	$divisions = array();
	$sql1 = "SELECT * FROM division";
	$result1 = mysql_query($sql1);
	while ($row = mysql_fetch_assoc($result1)) {
		$divisions[] = $row;
	}
	foreach ($divisions as $div) {
		$div_id = $div['id'];
		echo '<br />'.$div['name'].'</th>';
		$div_team_data = array();
		$sql3 = "SELECT * FROM team WHERE division_id = '$div_id'";
		$result3 = mysql_query($sql3);
		while ($row = mysql_fetch_assoc($result3)) {
			$div_team_data[] = $row;
		}
		$team_data = array();
		foreach ($div_team_data as $team) {
			$team_pts_for = 0;
			$team_pts_less = 0;
			$team_wins = 0;
			$team_losses = 0;
			$team_game_data = array();
			$sql = "SELECT * FROM game_scores WHERE (team1 = '".$team['name']."' OR team2 = '".$team['name']."')";
			$result = mysql_query($sql);
			while ($row = mysql_fetch_assoc($result)) {
				$team_game_data[] = $row;
			}
			foreach ($team_game_data as $game) {
				if ($game['team1'] == $team['name']) {
					$team_pts_for += $game['team1_score'];
					$team_pts_less += $game['team2_score'];
					if ($game['team1_score'] > $game['team2_score']) {
						$team_wins += 1;
					} else {
						$team_losses += 1;						
					}
				} elseif ($game['team2'] == $team['name']) {
					$team_pts_for += $game['team2_score'];
					$team_pts_less += $game['team1_score'];
					if ($game['team2_score'] > $game['team1_score']) {
						$team_wins += 1;
					} else {
						$team_losses += 1;						
					}
				}
			}
			$team_data[] = array('name'=>$team['name'],'wins'=>"$team_wins",'losses'=>"$team_losses",'points_for'=>"$team_pts_for",'points_against'=>"$team_pts_less");
			unset($sql);
			unset($result);
		}
		array_sort_higgyball($team_data,"wins","losses","points_for","points_against");
		echo '<table border="1"><tr><th>Name</th><th>Wins</th><th>Losses</th><th>For</th><th>Against</th></tr>';
		foreach ($team_data as $team) {
			echo '<tr><td>'.$team['name'].'</td><td>'.$team['wins'].'</td><td>'.$team['losses'].'</td><td>'.$team['points_for'].'</td><td>'.$team['points_against'].'</td></tr>';
		} 
		echo '</table>';
	} 

	function array_sort_higgyball(&$arr, $col1, $col2, $col3, $col4) {
		$sort = array();
		foreach ($arr as $key=>$val) {
			$sort[$col1][$key] = $val[$col1];
			$sort[$col2][$key] = $val[$col2];
			$sort[$col3][$key] = $val[$col3];
			$sort[$col4][$key] = $val[$col4];
		}

		array_multisort($sort[$col1], SORT_DESC, $sort[$col2], SORT_ASC, $sort[$col3], SORT_DESC, $sort[$col4], SORT_ASC, $arr);
	}
