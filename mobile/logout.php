<?php
	setcookie("higgy_password",$password,time()-3600*24*3,"/");
	header('Location: index.php');