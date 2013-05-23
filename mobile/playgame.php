<?php
	require_once('config.php');
	session_start();
	if (!isset($_SESSION['higgy_password'])) {
		header("Location: login.php?page=myteam");
	}
	$password = $_SESSION['higgy_password'];
	$sql = "SELECT * FROM team INNER JOIN users on team.id = users.team_id WHERE users.password = '$password' LIMIT 1";
	$result = mysql_query($sql);
	$my_data = mysql_fetch_assoc($result);
	$my_name = $my_data['name'];

	if (isset($_POST['submit_field'])) {
		$team1_id = $_POST['team1_id'];
		$team2_id = $_POST['team2_id'];
		$field_id = $_POST['field_id'];
		$sql1 = "SELECT name FROM team WHERE id = '$team1_id' LIMIT 1";
		$result1 = mysql_query($sql1);
		$team1_data = mysql_fetch_assoc($result1);
		$team1_name = $team1_data['name'];
		$sql2 = "SELECT name FROM team WHERE id = '$team2_id' LIMIT 1";
		$result2 = mysql_query($sql2);
		$team2_data = mysql_fetch_assoc($result2);
		$team2_name = $team2_data['name'];
		$other_name = $team2_name;
		$sql3 = "INSERT INTO score (score, created, updated) VALUES ('0', now(), now())";
		$result3 = mysql_query($sql3);
		$team1_score_id = mysql_insert_id();
		$sql4 = "INSERT INTO score (score, created, updated) VALUES ('0', now(), now())";
		$result4 = mysql_query($sql4);
		$team2_score_id = mysql_insert_id();
		$sql5 = "INSERT INTO game (team1_id, team1_score_id, team2_id, team2_score_id, field_id, is_complete, created, updated) 
			VALUES ('$team1_id', '$team1_score_id', '$team2_id', '$team2_score_id', '$field_id', '0', now(), now())";
		$result5 = mysql_query($sql5);
		$game_id = mysql_insert_id();
	} elseif (isset($_GET['game_id'])) {
		$game_id = $_GET['game_id'];
		$game_data = array();
		$sql6 = "SELECT game.*, team1.name as team1_name, team2.name as team2_name FROM game 
			INNER JOIN team team1 ON team1.id = game.team1_id
			INNER JOIN team team2 ON team2.id = game.team2_id
			WHERE game.id = '$game_id' LIMIT 1";
		$result6 = mysql_query($sql6);
		while ($row = mysql_fetch_assoc($result6)) {
			$game_data[] = $row;
		}
		echo $team1_name = $game_data[0]['team1_name'];
		$team2_name = $game_data[0]['team2_name'];
		if ($team1_name == $my_name) {
			$other_name = $team2_name;
		} else {
			$other_name = $team1_name;
		}
		$team1_score_id = $game_data[0]['team1_score_id'];
		$team2_score_id = $game_data[0]['team2_score_id'];
		$field_id = $game_data[0]['field_id'];
	}
?>
<?php require_once('includes/header.php'); ?>
		<div data-role="content">
			<h3>Now Playing Against</h3>
			<strong><?php echo $other_name; ?></strong><br />
			on Field <?php echo $field_id; ?>
			<form>
				<fieldset class="ui-grid-a">
					<div class="ui-block-a">
						<input data-clear-btn="false" name="team1_score" class="team_score" id="team1_score" value="" type="number">
						<label for="team1_score">Score for <?php echo $team1_name; ?></label>
					</div>
					<div class="ui-block-b">
						<input data-clear-btn="false" name="team2_score" class="team_score" id="team2_score" value="" type="number">
						<label for="team2_score">Score for <?php echo $team2_name; ?></label>
					</div>
				</fieldset>
				<input type="submit" name="submit" value="Submit" data-theme="b">
			</form>
		</div>
<?php require_once('includes/footer.php'); ?>