<?php
	require_once('config.php');

	$empty_submit = false;

	if (isset($_POST['submit_votes']) && !empty($_POST['select-female']) && !empty($_POST['select-male'])) {
		$ip_addr = '42.42.42.42';
		if (!empty($_POST['select-female'])) {
			$female = $_POST['select-female'];
			mysql_query("INSERT INTO `vote` (`name`, `gender`, `ip`) VALUES ('$female', 'F', '$ip_addr')");
			$fh = fopen('/home/mark/public_html/mobile/log.txt', 'a'); fputs($fh, date('Y-m-d H:i:s')." Deck MVP vote: $female\n"); fclose($fh);
		}
		if (!empty($_POST['select-male'])) {
			$male = $_POST['select-male'];
			mysql_query("INSERT INTO `vote` (`name`, `gender`, `ip`) VALUES ('$male', 'M', '$ip_addr')");
			$fh = fopen('/home/mark/public_html/mobile/log.txt', 'a'); fputs($fh, date('Y-m-d H:i:s')." Deck MVP vote: $male\n"); fclose($fh);
		}
		echo '<p>Thanks for voting!</p>';

	}
		$males = array();
		$females = array();
		$sql1 = "SELECT `name`, `person1`, `person1_gender`, `person2`, `person2_gender` FROM `team` WHERE `year_id` = 3";
		$result1 = mysql_query($sql1);
		while ($row = mysql_fetch_assoc($result1)) {
			//echo '<pre>'.print_r($row,1).'</pre>';
			switch($row['person1_gender']) {
				case 'F':
					$females[] = array('name'=>$row['person1'], 'team'=>$row['name']);
					break;
				case 'M':
					$males[] = array('name'=>$row['person1'], 'team'=>$row['name']);
					break;
			}
			switch($row['person2_gender']) {
				case 'F':
					$females[] = array('name'=>$row['person2'], 'team'=>$row['name']);
					break;
				case 'M':
					$males[] = array('name'=>$row['person2'], 'team'=>$row['name']);
					break;
			}
		}
		asort($females);
		asort($males);
?>
<p>&nbsp;</p>
<p>&nbsp;</p>
			<?php if ($empty_submit) echo '<p class="error">Please select two names below to vote</p>'; ?>
			<form method="post" action="<?= $_SERVER['PHP_SELF'] ?>" data-ajax="false">
				<div data-role="fieldcontain">
					<label for="select-female" class="select">Female</label>
					<select name="select-female" id="select-female" data-theme="e">
						<option value="" disabled="disabled" selected="">Please select a name</option>
						<?php foreach ($females as $option) { ?>
						<option value="<?= $option['name'].' ('.$option['team'].')' ?>"><?= $option['name'].' ('.$option['team'].')' ?></option>
						<?php } ?>
					</select>
					<label for="select-male" class="select">Male</label>
					<select name="select-male" id="select-male" data-theme="e">
						<option value="" disabled="disabled" selected="">Please select a name</option>
						<?php foreach ($males as $option) { ?>
						<option value="<?= $option['name'].' ('.$option['team'].')' ?>"><?= $option['name'].' ('.$option['team'].')' ?></option>
						<?php } ?>
					</select>
				</div>
				<input type="submit" name="submit_votes" value="Submit" data-theme="b">
			</form>
