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
			<h3>Welcome, <?php echo $my_name; ?>, to Higgyball 2014!</h3>
			<strong>Make sure you check in with the Deck Manager to begin play!</strong>
			<p>Use the menu icon at the top left to move around the app.</p>
			<ul>
				<li><a href="myteam.php" data-ajax="false">My Team</a> will show you the teams you've played and the teams you need to play.</li>
				<ul><li>Start a game by visiting the Deck Manager to get assigned a field. After the game, both teams have to enter the same score to complete the game. If one team doesn't have a phone, they can visit the Deck Manager to give them the score.</li></ul>
				<li><a href="scoreboard.php" data-ajax="false">Scoreboard</a> will show you all of the teams in the tournament and their records.</li>
				<li><a href="rules.php" data-ajax="false">Rules</a> gives you all of the rules of Higgyball.</li>
				<li><a href="map.php" data-ajax="false">Map</a> shows you a handy map of the fields.</li>
				<li><strong>New!</strong> <a href="mvp.php" data-ajax="false">MVP Voting</a> allows you to vote for the Higgyball11 Male and Female MVPs. Anyone can vote (once!)</li>
				<li><strong>New!</strong> <a href="camera.php" data-ajax="false">Photo Uploads</a> allows you to take a photo with your phone and upload it to the server. Keep it clean!</li>
			</ul>
			<p>Guest Users cannot enter scores, but can keep track of teams and standings on the <a href="scoreboard.php" data-ajax="false">Scoreboard</a> page.</p>
		</div><!-- /content -->
<?php require_once('includes/footer.php'); ?>