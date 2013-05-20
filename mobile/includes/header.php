<html>
<head>
	<title>Higgyball Scoresheet App</title>
	<link rel="stylesheet" type="text/css" href="includes/jquery.mobile-1.3.1.min.css" />
	<link rel="stylesheet" type="text/css" href="includes/jquery.mobile.theme-1.3.1.min.css" />
	<link rel="stylesheet" type="text/css" href="includes/higgy.mobile.css" />
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<script src="includes/jquery-1.9.1.min.js"></script>
	<script src="includes/jquery.mobile-1.3.1.min.js"></script>
	<script type="text/javascript" src="includes/higgy.mobile.js"></script>
	<script type="text/javascript" src="includes/slidernav.js"></script>
</head>
<body>
	<div id="menu">
		<ul data-theme="b" data-role="listview">
			<li><a href="index.php" class="contentLink" data-ajax="false">Home</a></li>
			<li><a href="myteam.php" class="contentLink" data-ajax="false">My Team</a></li>
			<li><a href="scores.php" class="contentLink" data-ajax="false">Enter Scores</a></li>
			<li><a href="scoreboard.php" class="contentLink" data-ajax="false">Scoreboard</a></li>
			<li><a href="rules.php" class="contentLink" data-ajax="false">Rules</a></li>
			<li><a href="map.php" class="contentLink" data-ajax="false">Map</a></li>
		</ul>
	</div>

	<div data-role="page" id="main">

		<div data-role="header">
			<a href="#" class="showMenu menuBtn">Menu</a>
			<img id="logo" src="/higgy-app/templates/higgy_app/images/logo.png" />
			<h1>Higgyball Scoresheet App</h1>
		</div><!-- /header -->
