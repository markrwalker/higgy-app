<?php
	require_once('config.php');
	if (isset($_GET['team'])) {
		$team_id = $_GET['team'];
	}
	$sql = "SELECT * FROM team where id = '$team_id' LIMIT 1";
	$result = mysql_query($sql);
	$team_data = mysql_fetch_assoc($result);

	if (isset($_COOKIE['higgy_password'])) {
		$password = $_COOKIE['higgy_password'];
		$sql = "SELECT * FROM users where team_id = '".$team_data['id']."' LIMIT 1";
		$result = mysql_query($sql);
		$user_data = mysql_fetch_assoc($result);
		if ($password == $user_data['password']) {
			header("Location: myteam.php");
			exit();
		}
	}
	
	$team_id = $team_data['id'];
	$team_name = $team_data['name'];
	$team_person1 = $team_data['person1'];
	$team_person2 = $team_data['person2'];
	$team_division_id = $team_data['division_id'];
	$team_year_id = $team_data['year_id'];
	$past_champion = $team_data['winner'];

	$team_game_count = 0;
	$team_plus_minus = 0;
	$team_wins = 0;
	$team_losses = 0;
	$team_sos = 0;
	$team_game_data = array();
	$teams_played = array();
	$sql2 = "SELECT * FROM game_scores WHERE (team1_id = $team_id OR team2_id = $team_id)";
	$result2 = mysql_query($sql2);
	while ($row = mysql_fetch_assoc($result2)) {
		$team_game_data[] = $row;
	}
	$i = 0;
	foreach ($team_game_data as $game) {
		$team2_id = '';
		$team_game_count++;
		if ($game['team1_id'] == $team_id) {
			$team2_id = $game['team2_id'];
			$team_plus_minus += $game['team1_score'];
			$team_plus_minus -= $game['team2_score'];
			$teams_played[$i]['opponent'] = $game['team2'];
			$teams_played[$i]['my_score'] = $game['team1_score'];
			$teams_played[$i]['their_score'] = $game['team2_score'];
			if ($game['team1_score'] > $game['team2_score']) {
				$team_wins += 1;
				$teams_played[$i]['result'] = 'W';
			} else {
				$team_losses += 1;						
				$teams_played[$i]['result'] = 'L';
			}
		} elseif ($game['team2_id'] == $team_id) {
			$team2_id = $game['team1_id'];
			$team_plus_minus += $game['team2_score'];
			$team_plus_minus -= $game['team1_score'];
			$teams_played[$i]['opponent'] = $game['team1'];
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
		$sql3 = "SELECT * FROM game_scores WHERE (team1_id = $team2_id OR team2_id = $team2_id)";
		$result3 = mysql_query($sql3);
		$opponent_game_data = array();
		while ($row = mysql_fetch_assoc($result3)) {
			$opponent_game_data[] = $row;
		}
		foreach ($opponent_game_data as $game) {
			if ($game['team1_id'] == $team2_id) {
				if ($game['team1_score'] > $game['team2_score']) {
					$team_sos += 1;
				}
			} elseif ($game['team2_id'] == $team2_id) {
				if ($game['team2_score'] > $game['team1_score']) {
					$team_sos += 1;
				}
			}
		}
	}
	$trophies = '';
	if ($past_champion) {
		$trophies = '<img class="ui-li-icon" src="images/trophy'.$past_champion.'.png">';
	}
?>
<?php require_once('includes/header.php'); ?>
		<div data-role="content">
			<h3><?php echo $trophies.' '.$team_name.' ('.$team_wins.' - '.$team_losses.')'; ?></h3>
			<h4><?php echo $team_person1.', '.$team_person2; ?></h4>
			<p>Games played: <?php echo $team_game_count; ?><br>
				Strength of Schedule: <?php echo $team_sos; ?><br>
				Differential: <?php echo $team_plus_minus; ?></p>
			<ul data-role="listview" data-inset="true" data-theme="c">
<?php foreach ($teams_played as $match) { ?>
				<li>vs <?php echo $match['opponent'].': '.$match['my_score'].' - '.$match['their_score'].' '.$match['result'].'<br />'; ?></li>
<?php } ?>
			</ul>
		</div><!-- /content -->
<?php require_once('includes/footer.php'); ?>