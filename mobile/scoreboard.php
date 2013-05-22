<?php
	require_once('config.php');

	$divisions = array();
	$sql1 = "SELECT * FROM division";
	$result1 = mysql_query($sql1);
	while ($row = mysql_fetch_assoc($result1)) {
		$divisions[] = $row;
	}
?>
<?php require_once('includes/header.php'); ?>
		<div data-role="content">
			<h3>Scoreboard</h3>
			<div data-role="collapsible-set" data-theme="c" data-content-theme="d">
<?php
	foreach ($divisions as $div) {
		$div_id = $div['id'];
?>
				<div data-role="collapsible">
					<h3><?php echo $div['name']; ?></h3>
					<div>
						<ul data-role="listview" data-inset="true" data-divider-theme="d">
<?php 
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
		array_sort_by_column($team_data,"wins",SORT_DESC);
		foreach ($team_data as $team) {
?>
							<li><a href="team.php?team=<?php echo urlencode($team['name']); ?>"><?php echo $team['name'].' ('.$team['wins'].' - '.$team['losses'].')'; ?></a></li>
<?php } ?>
						</ul>
					</div>
				</div>
<?php } ?>
			</div>
		</div><!-- /content -->
<?php require_once('includes/footer.php'); ?>
<?php
	function array_sort_by_column(&$arr, $col, $dir = SORT_ASC) {
		$sort_col = array();
		foreach ($arr as $key=> $row) {
			$sort_col[$key] = $row[$col];
		}

		array_multisort($sort_col, $dir, $arr);
	}
?>
