<html>
<head>
	<title>Higgyball Scoresheet App</title>
	<link rel="stylesheet" type="text/css" href="includes/jquery.mobile-1.3.1.min.css" />
	<link rel="stylesheet" type="text/css" href="includes/jquery.mobile.theme-1.3.1.min.css" />
	<link rel="stylesheet" type="text/css" href="includes/higgy.mobile.css" />
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<script src="includes/jquery-1.9.1.min.js"></script>
	<script src="includes/jquery.mobile-1.3.1.min.js"></script>
	<script type="text/javascript" src="includes/higgy.mobile.js"></script>
	<script type="text/javascript" src="includes/slidernav.js"></script>
</head>
<body>
<?php
	require_once('config.php');
	if (isset($_GET['id']) && isset($_GET['oid'])) {
		$team1_id = $_GET['id'];
		$team2_id = $_GET['oid'];
		$field_data = array();
		$sql1 = "SELECT id FROM field WHERE id NOT IN (SELECT field_id FROM game WHERE is_complete = 0)";
		$result1 = mysql_query($sql1);
		while ($row = mysql_fetch_assoc($result1)) {
			$field_data[] = $row;
		}
?>
	<div data-role="dialog" data-close-btn="none" id="setfield">
	
		<div data-role="header" data-theme="d">
			<h1>Request Game</h1>
		</div>

		<div data-role="content">
			<h3>Request A Game</h3>
			<form  method="POST" action="<?php echo $_SERVER['PHP_SELF'].'#playgame'; ?>">
				<fieldset data-role="controlgroup" data-mini="true">
					<legend>Select your field:</legend>
<?php foreach ($field_data as $field) { ?>
					<input name="field_id" id="radio-<?php echo $field['id']; ?>" value="<?php echo $field['id']; ?>" type="radio">
					<label for="radio-<?php echo $field['id']; ?>">Field <?php echo $field['id']; ?></label>
<?php } ?>
				</fieldset>
				<input type="hidden" name="team1_id" value="<?php echo $team1_id; ?>">
				<input type="hidden" name="team2_id" value="<?php echo $team2_id; ?>">
				<input type="submit" name="submit_field" value="Submit" data-theme="b" data-rel="dialog">
				<a href="dialog/index.html" data-role="button" data-rel="back" data-theme="c">Cancel</a>
			</form>
		</div>
	</div>
<?php
	} elseif (isset($_POST['submit_field'])) {
		$team1_id = $_POST['team1_id'];
		$team2_id = $_POST['team2_id'];
		$field_id = $_POST['field_id'];
		$sql2 = "SELECT name FROM team WHERE id = '$team2_id' LIMIT 1";
		$result2 = mysql_query($sql2);
		$team2_data = mysql_fetch_assoc($result2);
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
	<div data-role="dialog" data-close-btn="none" id="playgame">
	
		<div data-role="header" data-theme="d">
			<h1>Now Playing Against</h1>
		</div>

		<div data-role="content">
			<h3>Now Playing Against</h3>
			<strong><?php echo $team2_data['name']; ?></strong><br />
			on Field <?php echo $field_id; ?>
			<form>

			</form>
		</div>
	</div>
<?php } ?>
</body>
</html>