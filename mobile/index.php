<?php 
	require_once('includes/header.php');

	$user = '';
	if (empty($_SESSION['higgy_password'])) {
		$user = 'Guest User';
	} else {
		$user = $_SESSION['higgy_password'];
	}
?>
		<div data-role="content">
			<h3>Welcome, <?php echo $user; ?>, to Higgyball 2013!</h3>
			<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed aliquam massa a metus lobortis venenatis. Cras gravida laoreet tristique. Mauris vel purus quis odio viverra luctus. Sed sem nisi, elementum at egestas et, interdum sollicitudin ligula. Phasellus nec felis justo. In hac habitasse platea dictumst. Nam venenatis laoreet justo, sed commodo lacus volutpat a. Aenean quis quam nunc. Cras dolor ligula, laoreet pulvinar ultricies ut, posuere ac nisl. Nullam hendrerit rutrum mauris nec tempor. Proin vestibulum rhoncus diam nec pulvinar. Sed non justo ante.</p>
			<p>Aliquam vitae felis mi, tempor gravida nunc. Mauris nec risus elit, eu varius risus. Vivamus pulvinar pulvinar orci, non bibendum eros varius non. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla ipsum mauris, vulputate in consequat quis, dignissim vel urna. Sed purus quam, tristique at porttitor sed, feugiat ut urna. Morbi vestibulum pellentesque libero ac hendrerit. Morbi nibh purus, imperdiet id dapibus eu, iaculis egestas tellus. Nullam tempus semper tellus vestibulum consequat. Donec suscipit ipsum eget sem pretium dapibus.</p>
			<p>Ut congue eros quis metus adipiscing cursus. Phasellus cursus congue pharetra. Integer vitae mi nunc. Curabitur ac nisi nulla. Ut at neque et lorem consequat mollis ut vitae sem. Nam ullamcorper, augue id cursus egestas, libero tortor sodales augue, vel porttitor nulla dolor vel odio. Vivamus eget eros in libero bibendum ultricies nec sed augue.</p>
			<p>Aliquam et elit sed elit luctus dictum. Vivamus pellentesque fermentum lacinia. Maecenas vitae urna felis. Sed malesuada pulvinar dictum. Duis dui sem, porttitor id auctor a, gravida at sem. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Curabitur nulla libero, porta at viverra sed, fringilla ut lacus. Maecenas ac neque ipsum, a dictum dolor. Vivamus sollicitudin viverra ornare. Proin eu sem arcu, sodales eleifend sapien. Nam non nisl id massa auctor pharetra luctus eu orci.</p>
		</div><!-- /content -->
<?php require_once('includes/footer.php'); ?>