<?php

$host="localhost"; // Host name
$username="higgy"; // Mysql username
$password="vfCXV2zZjHzeFb5L"; // Mysql password
$db_name="higgy_app"; // Database name
$tbl_name="users"; // Table name

// Connect to server and select databse.
mysql_connect("$host", "$username", "$password")or die("cannot connect");
mysql_select_db("$db_name")or die("cannot select DB");

// username and password sent from form
$password = $_POST['password'];
$page = $_POST['page'];
$header = 'Location: index.php';

// To protect MySQL injection (more detail about MySQL injection)
$password = stripslashes($password);
$password = mysql_real_escape_string($password);

$sql="SELECT * FROM $tbl_name WHERE password='$password'";
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
