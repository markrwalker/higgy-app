<?php

include('config.php');

// username and password sent from form
$password = $_POST['password'];
$page = $_POST['page'];
$header = 'Location: index.php';

// To protect MySQL injection (more detail about MySQL injection)
$password = stripslashes($password);
$password = mysql_real_escape_string($password);

$sql="SELECT * FROM users WHERE password='$password'";
$result=mysql_query($sql);

// Mysql_num_row is counting table row
$count=mysql_num_rows($result);

if ($count==1) {
	session_register("higgy_user");
	header($header);
} else {
	include('includes/header.php'); ?>
		<div data-role="content">
			<?php echo $sql; ?>
			<p>Wrong PIN. Please contact the deck manager for the correct PIN.</p>
		</div><!-- /content -->
<?php 
	include('includes/footer.php');
}
?>
