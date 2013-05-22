<?php
	if ($_POST['submit']) {
		require_once('config.php');

		$password = $_POST['password'];
		if (!empty($_POST['page'])) {
			$page = $_POST['page'];
		} else {
			$page = 'myteam';
		}

		$password = stripslashes($password);
		$password = mysql_real_escape_string($password);

		$sql = "SELECT * FROM users WHERE password = '$password'";
		$result = mysql_query($sql);

		$count = mysql_num_rows($result);

		if ($count == 1) {
			session_start();
			$_SESSION['higgy_password'] = $password;
			header('Location: '.$page.'.php');
		} else {
			$error = "<p class=\"error\">Wrong PIN. Please contact the deck manager for the correct PIN.</p>\n";
		}
	}
	$page = $_GET['page']; 
?>
<?php require_once('includes/header.php'); ?>
		<div data-role="content">
			<?php if ($error) echo $error; ?>
			<form name="login" method="post" action="<?php echo $_SERVER['PHP_SELF'].'?page='.$page; ?>" data-ajax="false">
				<label for="password">Please enter your team's PIN:</label>
				<input name="password" id="password" value="" type="text">
				<input type="hidden" name="page" value="<?php echo $page; ?>">
				<input type="submit" name="submit" value="Submit" data-theme="c">
			</form>
		</div><!-- /content -->
<?php require_once('includes/footer.php'); ?>