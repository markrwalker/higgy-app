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
			<h3>Welcome, <?php echo $my_name; ?>, to Higgyball 2013!</h3>
			<strong>Make sure you check in with the Deck Manager to begin play!</strong>
			<p>Use the menu icon at the top left to move around the app.</p>
			<ul>
				<li><a href="myteam.php">My Team</a> will show you the teams you've played and the teams you need to play.</li>
				<ul><li>One team starts a game by clicking the name of one of your opponents on your My Team page, request a game, choose your field, and then both teams have to enter the same score afterward to complete the game. If one team doesn't have a phone, they can visit the Deck Manager to complete the game.</li></ul>
				<li><a href="scoreboard.php">Scoreboard</a> will show you all of the teams in the tournament and their records.</li>
				<li><a href="rules.php">Rules</a> gives you all of the rules of Higgyball.</li>
				<li><a href="map.php">Map</a> shows you a handy map of the fields.</li>
			</ul>
			<p>Guest Users cannot enter scores, but can keep track of teams and standings on the <a href="scoreboard.php">Scoreboard</a> page.</p>
		</div><!-- /content -->
<?php require_once('includes/footer.php'); ?>