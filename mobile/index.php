<?php
	require_once('config.php');
	$my_name = '';
	if (!isset($_COOKIE['higgy_password'])) {
		$my_name = 'Guest User';
	} else {
		$password = $_COOKIE['higgy_password'];
		$sql1 = "SELECT * FROM team INNER JOIN users on team.id = users.team_id WHERE users.password = '$password' LIMIT 1";
		$result1 = mysql_query($sql1);
		$error = mysql_error();
		$my_data = mysql_fetch_assoc($result1);
		$my_name = $my_data['name'];
	}
?>
<?php require_once('includes/header.php'); ?>
		<div data-role="content">
			<h3>Welcome, <?php echo $my_name; ?>, to Higgyball 2015!</h3>
		<?php if ($password && $my_data['checked_in'] == '0') { ?>
			<strong>Make sure you check in with the Deck Manager to begin play!</strong>
		<?php } ?>
			<p>Use the menu icon at the top left to move around the app.</p>
			<ul>
				<li><a href="myteam.php" data-ajax="false">My Team</a> will show you the teams you've played and the teams you need to play.</li>
				<ul><li>Start a game by visiting the Deck Manager to get assigned a field. <strong>Important!</strong> After the game, one or both teams have to report the score to the deck manager so that they know the field is open.</li></ul>
				<li><a href="scoreboard.php" data-ajax="false">Scoreboard</a> will show you all of the teams in the tournament and their records.</li>
				<li><a href="rules.php" data-ajax="false">Rules</a> gives you all of the rules of Higgyball.</li>
				<li><a href="map.php" data-ajax="false">Map</a> shows you a handy map of the fields.</li>
				<li><a href="mvp.php" data-ajax="false">MVP Voting</a> allows you to vote for the Higgyball12 Male and Female MVPs. Anyone can vote (once!)</li>
				<li><a href="camera.php" data-ajax="false">Photo Uploads</a> allows you to take a photo with your phone and upload it to the server. Keep it clean!</li>
			</ul>
			<p>Guest Users cannot login, but can keep track of teams and standings on the <a href="scoreboard.php" data-ajax="false">Scoreboard</a> page, as well as vote for <a href="mvp.php" data-ajax="false">MVPs</a> and <a href="camera.php" data-ajax="false">upload photos</a>.</p>
		</div><!-- /content -->
<?php require_once('includes/footer.php'); ?>