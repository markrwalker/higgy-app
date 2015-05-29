<?php
	if (isset($_POST['submit_login']) || isset($_GET['p'])) {
		require_once('config.php');

		if (isset($_POST['submit_login'])) $password = $_POST['password'];
		if (isset($_GET['p'])) $password = $_GET['p'];
		if (!empty($_POST['page'])) {
			$page = $_POST['page'];
		} else {
			$page = 'index';
		}

		$password = stripslashes($password);
		$password = mysql_real_escape_string($password);

		$sql = "SELECT u.*, t.name FROM users u 
		inner join team t on t.id = u.team_id
		WHERE u.password = '$password'";
		$result = mysql_query($sql);

		$count = mysql_num_rows($result);

		if ($count == 1) {
			$team = mysql_fetch_assoc($result);
			$fh = fopen('/home/mark/public_html/mobile/log.txt', 'a'); fputs($fh, date('Y-m-d H:i:s')." Login by: ".$team['name']."\n"); fclose($fh);
			setcookie("higgy_password",$password,time()+3600*24*3,"/");
			header('Location: '.$page.'.php');
			exit();
		} else {
			$error = "<p class=\"error\">Wrong PIN. Please contact the deck manager for the correct PIN.</p>\n";
		}
	}
	$page = $_GET['page']; 
?>
<?php require_once('includes/header.php'); ?>
		<div data-role="content">
			<?php if (isset($error)) echo $error; ?>
			<form method="post" action="<?php echo $_SERVER['PHP_SELF'].'?page='.$page; ?>" data-ajax="false">
				<label for="password">Please enter your team's PIN:</label>
     			<input data-clear-btn="true" name="password" id="password" value="" type="number">
				<input type="hidden" name="page" value="<?php echo $page; ?>">
				<input type="submit" name="submit_login" value="Submit" data-theme="b">
			</form>
		</div><!-- /content -->
<?php require_once('includes/footer.php'); ?>