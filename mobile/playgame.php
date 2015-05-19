<?php
	require_once('config.php');
	if (!isset($_COOKIE['higgy_password'])) {
		header("Location: login.php?page=myteam");
		exit();
	}
	$password = $_COOKIE['higgy_password'];
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
?>
<?php require_once('includes/header.php'); ?>
		<div data-role="content">
			<h3>Now Playing Against</h3>
			<strong><?php echo $other_name; ?></strong><br />
			on Field <?php echo $field_id; ?>
			<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" data-ajax="false">
				<fieldset class="ui-grid-a">
					<div class="ui-block-a">
						<input data-clear-btn="false" name="team1_score" class="team_score" id="team1_score" value="" type="number">
						<label for="team1_score">Score for <?php echo $team1_name; ?></label>
						<input type="hidden" name="team1_score_id" value="<?php echo $team1_score_id; ?>">
					</div>
					<div class="ui-block-b">
						<input data-clear-btn="false" name="team2_score" class="team_score" id="team2_score" value="" type="number">
						<label for="team2_score">Score for <?php echo $team2_name; ?></label>
						<input type="hidden" name="team2_score_id" value="<?php echo $team2_score_id; ?>">
					</div>
				</fieldset>
				<input type="hidden" name="game_id" value="<?php echo $game_id; ?>">
				<input type="submit" name="submit_scores" value="Enter Scores" data-theme="b">
			</form>
		</div>
<?php require_once('includes/footer.php'); ?>
<?php
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
?>
<?php require_once('includes/header.php'); ?>
		<div data-role="content">
			<h4>Now Playing Against</h4>
			<strong><?php echo $other_name; ?></strong><br />
			on Field <?php echo $field_id; ?>
		</div>
<?php require_once('includes/footer.php'); ?>
<?php
	} elseif (isset($_POST['submit_scores']) && isset($_POST['game_id'])) {
		$game_id = $_POST['game_id'];
		$team1_score_id = $_POST['team1_score_id'];
		$team1_score = $_POST['team1_score'];
		$team2_score_id = $_POST['team2_score_id'];
		$team2_score = $_POST['team2_score'];

		$score_data = array();
		$sql7 = "SELECT score1.score AS team1_score, score2.score AS team2_score FROM game 
			INNER JOIN score score1 ON game.team1_score_id = score1.id
			INNER JOIN score score2 ON game.team2_score_id = score2.id
			WHERE game.id = '$game_id'";
		$result7 = mysql_query($sql7);
		while ($row = mysql_fetch_assoc($result7)) {
			$score_data[] = $row;
		}

		if ($score_data[0]['team1_score'] == "0" && $score_data[0]['team2_score'] == "0") {
			$sql8 = "UPDATE score SET score = '$team1_score' WHERE id = '$team1_score_id'";
			$result8 = mysql_query($sql8);
			//if (mysql_error()) echo mysql_error(); die();
			$sql9 = "UPDATE score SET score = '$team2_score' WHERE id = '$team2_score_id'";
			$result9 = mysql_query($sql9);
			//if (mysql_error()) echo mysql_error(); die();
			setcookie('game_pending','13',time()+3600*24*3,"/");
?>
<?php require_once('includes/header.php'); ?>
		<div data-role="content">
			<h3>Your Scores Have Been Submitted</h3>
			<p>Please ensure the other team submits the same scores, or checks in with the Deck Manager to confirm the scores. 
				You will not be able to start your next game until you both verify your scores.</p>
			<p>Go to your <a href="myteam.php">My Team</a> page when ready for your next game.</p>
		</div>
<?php require_once('includes/footer.php'); ?>
<?php
		} elseif ($score_data[0]['team1_score'] == "$team1_score" && $score_data[0]['team2_score'] == "$team2_score") {
			$sql10 = "UPDATE game SET is_complete = 1 WHERE id = '$game_id'";
			$result10 = mysql_query($sql10);
			//if (mysql_error()) echo mysql_error(); die();
?>
<?php require_once('includes/header.php'); ?>
		<div data-role="content">
			<h3>Your Game Has Completed</h3>
			<p>Thank you for verifying your scores. You may go to your <a href="myteam.php">My Team</a> page to start your next game.</p>
		</div>
<?php require_once('includes/footer.php'); ?>
<?php
		} elseif ($score_data[0]['team1_score'] != "$team1_score" || $score_data[0]['team2_score'] != "$team2_score") {
?>
<?php require_once('includes/header.php'); ?>
		<div data-role="content">
			<h3 class="error">Your Scores Don't Match</h3>
			<p>Your scores don't match what your opponent entered. Please <a href="javascript:history.back()">go back a page</a> and try again, or visit the Deck Manager to enter the scores. 
				You may go to your <a href="myteam.php">My Team</a> page when ready for your next game.</p>
		</div>
<?php require_once('includes/footer.php'); ?>
<?php
		} else {
			echo '<pre>'.print_r($_POST,1).'</pre>'; die();
		}
	}
?>