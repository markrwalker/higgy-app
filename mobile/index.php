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
			<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed aliquam massa a metus lobortis venenatis. Cras gravida laoreet tristique. Mauris vel purus quis odio viverra luctus. Sed sem nisi, elementum at egestas et, interdum sollicitudin ligula. Phasellus nec felis justo. In hac habitasse platea dictumst. Nam venenatis laoreet justo, sed commodo lacus volutpat a. Aenean quis quam nunc. Cras dolor ligula, laoreet pulvinar ultricies ut, posuere ac nisl. Nullam hendrerit rutrum mauris nec tempor. Proin vestibulum rhoncus diam nec pulvinar. Sed non justo ante.</p>
			<p>Aliquam vitae felis mi, tempor gravida nunc. Mauris nec risus elit, eu varius risus. Vivamus pulvinar pulvinar orci, non bibendum eros varius non. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla ipsum mauris, vulputate in consequat quis, dignissim vel urna. Sed purus quam, tristique at porttitor sed, feugiat ut urna. Morbi vestibulum pellentesque libero ac hendrerit. Morbi nibh purus, imperdiet id dapibus eu, iaculis egestas tellus. Nullam tempus semper tellus vestibulum consequat. Donec suscipit ipsum eget sem pretium dapibus.</p>
		</div><!-- /content -->
<?php require_once('includes/footer.php'); ?>