<?php
	require_once('config.php');
	if (!isset($_COOKIE['higgy_password'])) {
		header("Location: login.php?page=myteam");
		exit();
	}
	$password = $_COOKIE['higgy_password'];
	$sql1 = "SELECT team.* FROM team INNER JOIN users ON team.id = users.team_id WHERE users.password = '$password' LIMIT 1";
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
	$my_past_champion = $my_data['winner'];
	$my_checked_in = $my_data['checked_in'];

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
	$my_plus_minus = 0;
	$my_wins = 0;
	$my_losses = 0;
	$my_sos = 0;
	$my_game_data = array();
	$my_teams_played = array();
	$sql3 = "SELECT * FROM game_scores WHERE (team1_id = '$my_id' OR team2_id = '$my_id')";
	$result3 = mysql_query($sql3);
	while ($row = mysql_fetch_assoc($result3)) {
		$my_game_data[] = $row;
	}
	$i = 0;
	foreach ($my_game_data as $game) {
		$team2_id = '';
		$my_game_count++;
		if ($game['team1_id'] == $my_id) {
			$team2_id = $game['team2_id'];
			$my_plus_minus += $game['team1_score'];
			$my_plus_minus -= $game['team2_score'];
			$my_teams_played[$i]['opponent'] = $game['team2'];
			$my_teams_played[$i]['my_score'] = $game['team1_score'];
			$my_teams_played[$i]['their_score'] = $game['team2_score'];
			if ($game['team1_score'] > $game['team2_score']) {
				$my_wins += 1;
				$my_teams_played[$i]['result'] = 'W';
			} else {
				$my_losses += 1;						
				$my_teams_played[$i]['result'] = 'L';
			}
		} elseif ($game['team2_id'] == $my_id) {
			$team2_id = $game['team1_id'];
			$my_plus_minus += $game['team2_score'];
			$my_plus_minus -= $game['team1_score'];
			$my_teams_played[$i]['opponent'] = $game['team1'];
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
		$sql5 = "SELECT * FROM game_scores WHERE (team1_id = $team2_id OR team2_id = $team2_id)";
		$result5 = mysql_query($sql5);
		$opponent_game_data = array();
		while ($row = mysql_fetch_assoc($result5)) {
			$opponent_game_data[] = $row;
		}
		foreach ($opponent_game_data as $game) {
			if ($game['team1_id'] == $team2_id) {
				if ($game['team1_score'] > $game['team2_score']) {
					$my_sos += 1;
				}
			} elseif ($game['team2_id'] == $team2_id) {
				if ($game['team2_score'] > $game['team1_score']) {
					$my_sos += 1;
				}
			}
		}
	}
	$trophies = '';
	if ($my_past_champion) {
		$trophies = '<img class="ui-li-icon" src="images/trophy'.$my_past_champion.'.png">';
	}
?>
<?php require_once('includes/header.php'); ?>
		<div data-role="content">
<?php if ($my_checked_in == '0') { ?>
			<h2 class="error"><?php echo $my_name; ?>, please check in with the Deck Manager to begin play!</h2>
<?php exit(); } ?>
			<h3><?php echo $trophies.' '.$my_name.' ('.$my_wins.' - '.$my_losses.')'; ?></h3>
			<h4><?php echo $my_person1.', '.$my_person2; ?></h4>
			<p>Games played: <?php echo $my_game_count; ?><br>
				Strength of Schedule: <?php echo $my_sos; ?><br>
				Differential: <?php echo $my_plus_minus; ?></p>
			<ul data-role="listview" data-inset="true" data-theme="c">
<?php foreach ($my_teams_played as $match) { ?>
				<li>vs <?php echo $match['opponent'].': '.$match['my_score'].' - '.$match['their_score'].' '.$match['result'].'<br />'; ?></li>
<?php } ?>
			</ul>
<?php
	$sql4 = "SELECT game.team1_id, game.team2_id, t1.name AS 't1_name', t2.name AS 't2_name' FROM game 
		INNER JOIN team t1 ON game.team1_id = t1.id
		INNER JOIN team t2 ON game.team2_id = t2.id
		WHERE (team1_id = $my_id OR team2_id = $my_id) AND is_complete = 0
	";
	$result4 = mysql_query($sql4);
	if (mysql_num_rows($result4)) {
		$current_match = mysql_fetch_assoc($result4);
		$opponent_name = $current_match['t1_name'] == $my_name ? $current_match['t2_name'] : $current_match['t1_name'];
?>
			<h4>Need to find a field against:</h4>
			<ul data-role="listview" data-inset="true" data-theme="c">
				<li><?php echo $opponent_name; ?></li>
			</ul>
<?php } ?>
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