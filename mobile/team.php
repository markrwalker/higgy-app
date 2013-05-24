<?php
	require_once('config.php');
	if (isset($_GET['team'])) {
		$team_name = urldecode($_GET['team']);
	}
	$sql = "SELECT * FROM team where name = '$team_name' LIMIT 1";
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
	
	$sql1 = "SELECT * FROM team WHERE id = '".$team_data['id']."' LIMIT 1";
	$result1 = mysql_query($sql1);
	$my_data = mysql_fetch_assoc($result1);
	$my_id = $my_data['id'];
	$my_name = $my_data['name'];
	$my_person1 = $my_data['person1'];
	$my_person2 = $my_data['person2'];
	$my_division_id = $my_data['division_id'];
	$my_year_id = $my_data['year_id'];

	$sqlx = "SELECT name from division where id = $my_division_id LIMIT 1";
	$resultx = mysql_query($sqlx);
	$division_data = mysql_fetch_assoc($resultx);
	$my_division_name = $division_data['name'];

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
			<h4><?php echo $my_person1.', '.$my_person2; ?>
				<?php echo $my_division_name; ?> Division</h4>
			<p>Points for: <?php echo $my_pts_for; ?><br />
				Points against: <?php echo $my_pts_less; ?></p>
			<h4>Games played: <?php echo $my_game_count; ?></h4>
			<ul data-role="listview">
<?php foreach ($teams_played as $match) { ?>
				<li>vs <?php echo $match['team_name'].': '.$match['my_score'].' - '.$match['their_score'].' '.$match['result'].'<br />'; ?></li>
<?php } ?>
			</ul>
		</div><!-- /content -->
<?php require_once('includes/footer.php'); ?>