<?php
	session_start();
	session_unset();
	session_destroy();

	if (empty($_SESSION['higgy_password'])) {
		header('Location: index.php');
	}