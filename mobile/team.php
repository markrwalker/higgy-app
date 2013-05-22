<?php
	require_once('config.php');
	if (isset($_GET['team'])) {
		$team_name = urldecode($_GET['team']);
	}
	$sql = "SELECT * FROM team where name = '$team_name' LIMIT 1";
	$result = mysql_query($sql);
	$team_data = mysql_fetch_assoc($result);
	session_start();
	if (isset($_SESSION['higgy_password'])) {
		$password = $_SESSION['higgy_password'];
		$sql = "SELECT * FROM users where team_id = '".$team_data['id']."' LIMIT 1";
		$result = mysql_query($sql);
		$user_data = mysql_fetch_assoc($result);
		if ($password == $user_data['password']) {
			header("Location: myteam.php");
		}
	}
	
	$sql1 = "SELECT * FROM team WHERE id = '".$team_data['id']."' LIMIT 1";
	$result1 = mysql_query($sql1);
	$my_data = mysql_fetch_assoc($result1);
	$my_id = $my_data['id'];
	$my_name = $my_data['name'];
	$my_person1 = $my_data['person1'];
	$my_person2 = $my_data['person2'];
	$my_division_id = $my_data['division_id'];
	$my_year_id = $my_data['year_id'];

	$my_game_count = 0;
	$my_pts_for = 0;
	$my_pts_less = 0;
	$my_wins = 0;
	$my_losses = 0;
	$my_game_data = array();
	$teams_played = array();
	$sql2 = "SELECT * FROM game_scores WHERE (team1 = '$my_name' OR team2 = '$my_name')";
	$result2 = mysql_query($sql2);
	while ($row = mysql_fetch_assoc($result2)) {
		$my_game_data[] = $row;
	}
	$i = 0;
	foreach ($my_game_data as $game) {
		$my_game_count++;
		if ($game['team1'] == $my_name) {
			$my_pts_for += $game['team1_score'];
			$my_pts_less += $game['team2_score'];
			$teams_played[$i]['team_name'] = $game['team2'];
			$teams_played[$i]['my_score'] = $game['team1_score'];
			$teams_played[$i]['their_score'] = $game['team2_score'];
			if ($game['team1_score'] > $game['team2_score']) {
				$my_wins += 1;
				$teams_played[$i]['result'] = 'W';
			} else {
				$my_losses += 1;						
				$teams_played[$i]['result'] = 'L';
			}
		} elseif ($game['team2'] == $my_name) {
			$my_pts_for += $game['team2_score'];
			$my_pts_less += $game['team1_score'];
			$teams_played[$i]['team_name'] = $game['team1'];
			$teams_played[$i]['my_score'] = $game['team2_score'];
			$teams_played[$i]['their_score'] = $game['team1_score'];
			if ($game['team2_score'] > $game['team1_score']) {
				$my_wins += 1;
				$teams_played[$i]['result'] = 'W';
			} else {
				$my_losses += 1;						
				$teams_played[$i]['result'] = 'L';
			}
		}
		$i++;
	}
?>
<?php require_once('includes/header.php'); ?>
		<div data-role="content">
			<h3><?php echo $my_name.' ('.$my_wins.' - '.$my_losses.')'; ?></h3>
			<h4><?php echo $my_person1.', '.$my_person2; ?></h4>
			<p>Points for: <?php echo $my_pts_for; ?><br />
				Points against: <?php echo $my_pts_less; ?></p>
				Games played: <?php echo $my_game_count; ?><br />
<?php foreach ($teams_played as $match) { ?>
				vs <?php echo $match['team_name'].': '.$match['my_score'].' - '.$match['their_score'].' '.$match['result'].'<br />'; ?>
<?php } ?>
			<div data-role="collapsible-set" data-theme="c" data-content-theme="d">
<?php 
	$div_team_data = array();
	$sql3 = "SELECT * FROM team WHERE division_id = '$my_division_id'";
	$result3 = mysql_query($sql3);
	while ($row = mysql_fetch_assoc($result3)) {
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
		}
		unset($sql);
		unset($result);
?>
				<div data-role="collapsible">
					<h3><?php echo $team['name'].' ('.$team_wins.' - '.$team_losses.')'; ?></h3>
					<div>
						<strong><?php echo $team['person1'].', '.$team['person2']; ?></strong>
						<p>Points for: <?php echo $team_pts_for; ?><br />
							Points against: <?php echo $team_pts_less; ?></p>
							Games played: <?php echo $team_game_count; ?><br />
<?php foreach ($teams_played as $match) { ?>
							vs <?php echo $match['team_name'].': '.$match['my_score'].' - '.$match['their_score'].' '.$match['result'].'<br />'; ?>
<?php } ?>
					</div>
				</div>
<?php } ?>
		</div><!-- /content -->
<?php require_once('includes/footer.php'); ?>