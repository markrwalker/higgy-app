<?php
	session_start();
	if(!isset($_SESSION['higgy_password'])) {
		header("Location: login.php?page=scores");
	}
?>
<?php require_once('includes/header.php'); ?>
		<div data-role="content">
			<p>Enter scores.</p>
		</div><!-- /content -->
<?php require_once('includes/footer.php'); ?>