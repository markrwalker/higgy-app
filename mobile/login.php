<?php include('includes/header.php'); ?>
<?php $page = $_GET['page']; ?>
		<div data-role="content">
			<form name="lgoin" method="post" action="checklogin.php">
				<label for="password">Please enter your team's PIN:</label>
				<input name="password" id="password" value="" type="text">
				<input type="hidden" name="page" value="<?php echo $page; ?>">
				<input type="submit" value="Submit">
			</form>
		</div><!-- /content -->
<?php include('includes/footer.php'); ?>