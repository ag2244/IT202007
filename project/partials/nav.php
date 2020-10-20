<link rel="stylesheet" href="static/css/styles.css">

<?php
//we'll be including this on most/all pages so it's a good place to include anything else we want on those pages
require_once(__DIR__ . "/../lib/helpers.php");
?>
<nav>
<!-- makes this a nav bar -->
<ul class = "nav">
	<!-- Navigate to Home (home.php) button -->
	<li><a href="home.php">Home</a></li>
	
	<!-- If not logged in, put a log in and register button -->
	<?php if(!is_logged_in()):?>
		<li><a href="login.php">Login</a></li>
		<li><a href="register.php">Register</a></li>
	<?php endif;?>
	
	<!-- If logged in, put a logout button -->
	<?php if(is_logged_in()):?>
		<li><a href="profile.php">Profile</a></li>
		<li><a href="logout.php">Logout</a></li>
	<?php endif; ?>
</ul>
</nav>