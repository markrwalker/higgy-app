<?php

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
	require_once('includes/header.php'); ?>
		<div data-role="content">
			<p class="error">Wrong PIN. Please contact the deck manager for the correct PIN.</p>
		</div><!-- /content -->
<?php 
	require_once('includes/footer.php');
}
?>
