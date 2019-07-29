<?php
	require_once('display/inc/config.php');

	if (!empty($_GET['year_id'])) {
		$year_id = $_GET['year_id'];
	}

	$games = array();
	$sql1 = "SELECT * FROM `game` WHERE `year_id` = $year_id AND `is_complete` = 1";
	$result1 = mysqli_query($db, $sql1);
	while ($row = mysqli_fetch_assoc($result1)) {
		$games[$row['team1_id']][] = $row['field_id'];
		$games[$row['team2_id']][] = $row['field_id'];
	}

	foreach ($games as $id => $fields) {
		$dup = false;
		$fields_orig = $fields;
		foreach ($fields as $x => $field) {
			unset($fields[$x]);
			if (in_array($field, $fields)) {
				$dup = true;
			}
		}
		if ($dup) {
			echo $id.'<pre>'.print_r($fields_orig,1).'</pre>';
		}
	}

