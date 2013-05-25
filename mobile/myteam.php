<?php
	require_once('config.php');
	if (!isset($_COOKIE['higgy_password'])) {
		header("Location: login.php?page=myteam");
		exit();
	}
	$password = $_COOKIE['higgy_password'];
	$sql1 = "SELECT * FROM team INNER JOIN users on team.id = users.team_id WHERE users.password = '$password' LIMIT 1";
	$result1 = mysql_query($sql1);
	$my_data = mysql_fetch_assoc($result1);
	if (!$my_data) {
		header("Location: login.php?page=myteam");
		exit();
	}
	$my_id = $my_data['id'];
	$my_name = $my_data['name'];
	$my_person1 = $my_data['person1'];
	$my_person2 = $my_data['person2'];
	$my_division_id = $my_data['division_id'];
	$my_year_id = $my_data['year_id'];
	$my_checked_in = $my_data['checked_in'];

	$sqlx = "SELECT name from division where id = $my_division_id LIMIT 1";
	$resultx = mysql_query($sqlx);
	$division_data = mysql_fetch_assoc($resultx);
	$my_division_name = $division_data['name'];

	$sql2 = "SELECT id from game WHERE (team1_id = '$my_id' OR team2_id = '$my_id') AND is_complete = 0 LIMIT 1";
	$result2 = mysql_query($sql2);
	$current_game_data = mysql_fetch_assoc($result2);
	if ($current_game_data && isset($_COOKIE['game_pending'])) {
?>
<?php require_once('includes/header.php'); ?>
		<div data-role="content">
			<h3 class="error">Your Game Is Stll Pending</h3>
			<p>Please contact the last team you played to enter that game's scores, or speak to the Deck Manager about resolving the game. 
				Then go to your <a href="myteam.php">My Team</a> page when ready for your next game.</p>
		</div>
<?php require_once('includes/footer.php'); ?>
<?php
		exit();
	} elseif ($current_game_data) {
		$game_id = $current_game_data['id'];
		header("Location: playgame.php?game_id=".$game_id);
		exit();
	} else {
		setcookie('game_pending','13',time()-3600*24*3,"/");
	}

	$my_game_count = 0;
	$my_pts_for = 0;
	$my_pts_less = 0;
	$my_wins = 0;
	$my_losses = 0;
	$my_game_data = array();
	$my_teams_played = array();
	$sql3 = "SELECT * FROM game_scores WHERE (team1 = '$my_name' OR team2 = '$my_name')";
	$result3 = mysql_query($sql3);
	while ($row = mysql_fetch_assoc($result3)) {
		$my_game_data[] = $row;
	}
	$i = 0;
	foreach ($my_game_data as $game) {
		$my_game_count++;
		if ($game['team1'] == $my_name) {
			$my_pts_for += $game['team1_score'];
			$my_pts_less += $game['team2_score'];
			$my_teams_played[$i]['team_name'] = $game['team2'];
			$my_teams_played[$i]['my_score'] = $game['team1_score'];
			$my_teams_played[$i]['their_score'] = $game['team2_score'];
			if ($game['team1_score'] > $game['team2_score']) {
				$my_wins += 1;
				$my_teams_played[$i]['result'] = 'W';
			} else {
				$my_losses += 1;						
				$my_teams_played[$i]['result'] = 'L';
			}
		} elseif ($game['team2'] == $my_name) {
			$my_pts_for += $game['team2_score'];
			$my_pts_less += $game['team1_score'];
			$my_teams_played[$i]['team_name'] = $game['team1'];
			$my_teams_played[$i]['my_score'] = $game['team2_score'];
			$my_teams_played[$i]['their_score'] = $game['team1_score'];
			if ($game['team2_score'] > $game['team1_score']) {
				$my_wins += 1;
				$my_teams_played[$i]['result'] = 'W';
			} else {
				$my_losses += 1;						
				$my_teams_played[$i]['result'] = 'L';
			}
		}
		$i++;
	}
?>
<?php require_once('includes/header.php'); ?>
		<div data-role="content">
<?php if ($my_checked_in == '0') { ?>
			<h2 class="error"><?php echo $my_name; ?>, please check in with the Deck Manager to begin play!</h2>
<?php exit(); } ?>
			<h3><?php echo $my_name.' ('.$my_wins.' - '.$my_losses.')'; ?></h3>
			<h4><?php echo $my_person1.', '.$my_person2; ?><br />
				<?php echo $my_division_name; ?> Division</h4>
			<p>Points for: <?php echo $my_pts_for; ?><br />
				Points against: <?php echo $my_pts_less; ?></p>
			<h4>Games played: <?php echo $my_game_count; ?></h4>
			<ul data-role="listview" data-inset="true" data-theme="c">
<?php foreach ($my_teams_played as $match) { ?>
				<li>vs <?php echo $match['team_name'].': '.$match['my_score'].' - '.$match['their_score'].' '.$match['result'].'<br />'; ?></li>
<?php } ?>
			</ul>
			<h4>Still need to play:</h4>
			<div data-role="collapsible-set" data-theme="b" data-content-theme="d">
<?php 
	$div_team_data = array();
	$sql4 = "SELECT * FROM team WHERE division_id = $my_division_id AND checked_in = 1 AND team.name NOT IN (
		SELECT team1 FROM game_scores WHERE team2 = '$my_name'
	) AND team.name NOT IN (
		SELECT team2 FROM game_scores WHERE team1 = '$my_name'
	)";
	$result4 = mysql_query($sql4);
	while ($row = mysql_fetch_assoc($result4)) {
		$div_team_data[] = $row;
	}
	foreach ($div_team_data as $team) { 
		if ($team['name'] == $my_name) continue;
		$team_game_count = 0;
		$team_pts_for = 0;
		$team_pts_less = 0;
		$team_wins = 0;
		$team_losses = 0;
		$team_game_data = array();
		$teams_played = array();
		$sql = "SELECT * FROM game_scores WHERE (team1 = '".$team['name']."' OR team2 = '".$team['name']."')";
		$result = mysql_query($sql);
		while ($row = mysql_fetch_assoc($result)) {
			$team_game_data[] = $row;
		}
		$i = 0;
		foreach ($team_game_data as $game) {
			$team_game_count++;
			if ($game['team1'] == $team['name']) {
				$team_pts_for += $game['team1_score'];
				$team_pts_less += $game['team2_score'];
				$teams_played[$i]['team_name'] = $game['team2'];
				$teams_played[$i]['my_score'] = $game['team1_score'];
				$teams_played[$i]['their_score'] = $game['team2_score'];
				if ($game['team1_score'] > $game['team2_score']) {
					$team_wins += 1;
					$teams_played[$i]['result'] = 'W';
				} else {
					$team_losses += 1;						
					$teams_played[$i]['result'] = 'L';
				}
			} elseif ($game['team2'] == $team['name']) {
				$team_pts_for += $game['team2_score'];
				$team_pts_less += $game['team1_score'];
				$teams_played[$i]['team_name'] = $game['team1'];
				$teams_played[$i]['my_score'] = $game['team2_score'];
				$teams_played[$i]['their_score'] = $game['team1_score'];
				if ($game['team2_score'] > $game['team1_score']) {
					$team_wins += 1;
					$teams_played[$i]['result'] = 'W';
				} else {
					$team_losses += 1;						
					$teams_played[$i]['result'] = 'L';
				}
			}
			$i++;
		} //foreach $game
		unset($sql);
		unset($result);
		$is_playing = false;
		$sqlz = "SELECT * FROM game WHERE (team1_id = '".$team['id']."' OR team2_id = '".$team['id']."') AND is_complete = 0";
		$resultz = mysql_query($sqlz);
		$count = mysql_num_rows($resultz);
		if ($count > 0) $is_playing = true;
?>
				<div data-role="collapsible">
					<h3><?php echo $team['name'].' ('.$team_wins.' - '.$team_losses.')'; ?></h3>
					<div>
						<strong><?php echo $team['person1'].', '.$team['person2']; ?></strong>
						<?php if ($is_playing) { echo '<div class="error">Currently playing</div>'; ?>
						<?php } elseif (!in_array_r($team['name'], $my_teams_played)) { echo '<div><a href="setfield.php?id='.$my_id.'&oid='.$team['id'].'" data-role="button" data-rel="dialog" data-transition="slidedown" data-inline="true" data-theme="b">Request Game</a></div>'; } ?>
						<p>Points for: <?php echo $team_pts_for; ?><br />
							Points against: <?php echo $team_pts_less; ?></p>
							Games played: <?php echo $team_game_count; ?><br />
<?php foreach ($teams_played as $match) { ?>
							vs <?php echo $match['team_name'].': '.$match['my_score'].' - '.$match['their_score'].' '.$match['result'].'<br />'; ?>
<?php } //foreach $match ?>
					</div>
				</div>
<?php } //foreach $team ?>
			</div>
		</div><!-- /content -->
<?php require_once('includes/footer.php'); ?>
<?php
	function in_array_r($needle, $haystack, $strict = false) {
		foreach ($haystack as $item) {
			if (($strict ? $item === $needle : $item == $needle) || (is_array($item) && in_array_r($needle, $item, $strict))) {
				return true;
			}
		}
		return false;
	}
?>