<?php
	require_once('config.php');
	$year = array();
	$sql1 = "SELECT * FROM `year` WHERE `current` = 1";
	$result1 = mysql_query($sql1);
	$year = mysql_fetch_assoc($result1);
?>
<?php require_once('includes/header.php'); ?>
		<div data-role="content">
			<h3>Scoreboard</h3>
			<div data-role="collapsible-set" data-theme="b" data-content-theme="d">
				<div>
					<ul data-role="listview" data-inset="true" data-divider-theme="d">
<?php 
		$teams = array();
		$sql3 = "SELECT * FROM team WHERE year_id = ".$year['id']." AND checked_in = 1";
		$result3 = mysql_query($sql3);
		while ($row = mysql_fetch_assoc($result3)) {
			$teams[] = $row;
		}
		$team_data = array();
		foreach ($teams as $team) {
			$team_plus_minus = 0;
			$team_wins = 0;
			$team_losses = 0;
			$sos = 0;
			$team_game_data = array();
			$sql = "SELECT * FROM game_scores WHERE (team1_id = '".$team['id']."' OR team2_id = '".$team['id']."')";
			$result = mysql_query($sql);
			while ($row = mysql_fetch_assoc($result)) {
				$team_game_data[] = $row;
			}
			foreach ($team_game_data as $game) {
				$team2_id = '';
				if ($game['team1_id'] == $team['id']) {
					$team2_id = $game['team2_id'];
					$team_plus_minus += $game['team1_score'];
					$team_plus_minus -= $game['team2_score'];
					if ($game['team1_score'] > $game['team2_score']) {
						$team_wins += 1;
					} else {
						$team_losses += 1;
					}
				} elseif ($game['team2_id'] == $team['id']) {
					$team2_id = $game['team1_id'];
					$team_plus_minus += $game['team2_score'];
					$team_plus_minus -= $game['team1_score'];
					if ($game['team2_score'] > $game['team1_score']) {
						$team_wins += 1;
					} else {
						$team_losses += 1;
					}
				}
				$sql2 = "SELECT * FROM game_scores WHERE (team1_id = $team2_id OR team2_id = $team2_id)";
				$result2 = mysql_query($sql2);
				$opponent_game_data = array();
				while ($row = mysql_fetch_assoc($result2)) {
					$opponent_game_data[] = $row;
				}
				foreach ($opponent_game_data as $game) {
					if ($game['team1_id'] == $team2_id) {
						if ($game['team1_score'] > $game['team2_score']) {
							$sos += 1;
						}
					} elseif ($game['team2_id'] == $team2_id) {
						if ($game['team2_score'] > $game['team1_score']) {
							$sos += 1;
						}
					}
				}
			}
			$team_data[] = array('id'=>$team['id'], 'name'=>$team['name'],'wins'=>"$team_wins",'losses'=>"$team_losses",'plus_minus'=>"$team_plus_minus",'sos'=>"$sos");
			unset($sql);
			unset($result);
		}
		array_sort_higgyball($team_data,"wins","losses","plus_minus","sos");
		foreach ($team_data as $team) {
?>
						<li><a href="team.php?team=<?php echo $team['id']; ?>"><?php echo $team['name'].' ('.$team['wins'].' - '.$team['losses'].')'; ?></a></li>
<?php } ?>
					</ul>
				</div>
			</div>
		</div><!-- /content -->
<?php require_once('includes/footer.php'); ?>
<?php
	function array_sort_higgyball(&$arr, $col1, $col2, $col3, $col4) {
		$sort = array();
		foreach ($arr as $key=>$val) {
			$sort[$col1][$key] = $val[$col1];
			$sort[$col2][$key] = $val[$col2];
			$sort[$col3][$key] = $val[$col3];
			$sort[$col4][$key] = $val[$col4];
		}

		array_multisort($sort[$col1], SORT_DESC, $sort[$col2], SORT_ASC, $sort[$col3], SORT_DESC, $sort[$col4], SORT_DESC, $arr);
	}
?>