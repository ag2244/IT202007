<?php
require_once(__DIR__ . "/../lib/helpers.php");
?>

<!-- CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" 
	integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" 
	crossorigin="anonymous">

<!-- jQuery and JS bundle w/ Popper.js -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" 
	integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" 
	crossorigin="anonymous"></script>
	
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js" 
	integrity="sha384-ho+j7jyWK8fNQe+A12Hb8AhRq26LrZ/JpcUGGOn+Y7RsweNrtN/tE3MoK7ZeZDyx" 
	crossorigin="anonymous"></script>

<nav class="navbar navbar-expand-lg navbar-dark" style="background-color: #326298;">
<!-- makes this a nav bar -->
<ul class="navbar-nav mr-auto">
	<!-- Navigate to Home (home.php) button -->
	<li class="nav-item"><a class="nav-link" href="<?php echo getURL("home.php");?>">Home</a></li>
	
	<li class="nav-item"><a class="nav-link" href="<?php echo getURL("spaceRocks.php");?>">Space Rocks Game</a></li>
	
	<!-- If not logged in, put a log in and register button -->
	<?php if(!is_logged_in()):?>
		<li class="nav-item"><a class="nav-link" href="<?php echo getURL("login.php"); ?>">Login</a></li>
		
		<li class="nav-item"><a class="nav-link" href="<?php echo getURL("register.php"); ?>">Register</a></li>
		
	<?php endif;?>
	
	<!-- If logged in, put a logout button -->
	<?php if(is_logged_in()):?>
		
		<li class="nav-item"><a class="nav-link" href="<?php echo getURL("myScores.php"); ?>">My Scores</a></li>
		
		<li class="nav-item dropdown">
			<a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown"
			   aria-haspopup="true" aria-expanded="false">
				Competitions
			</a>

			<div class="dropdown-menu" aria-labelledby="navbarDropdown">
			
				<a class="dropdown-item" href="<?php echo getURL("competitions.php"); ?>">Competitions</a>
				
				<a class="dropdown-item" href="<?php echo getURL("competitionHistory.php"); ?>">Competition History</a>
			
				<a class="dropdown-item" href="<?php echo getURL("makeCompetition.php"); ?>">Start a Competition</a>
			
			</div>
		</li>
		
		<li class="nav-item"><a class="nav-link" href="<?php echo getURL("profile.php"); ?>">Profile</a></li>
		
		<li class="nav-item"><a class="nav-link" href="<?php echo getURL("logout.php"); ?>">Logout</a></li>
	<?php endif; ?>
	
	<?php if (has_role("Admin")): ?>
	
		<li class="nav-item dropdown">
			<a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown"
			   aria-haspopup="true" aria-expanded="false">
				Admin
			</a>

			<div class="dropdown-menu" aria-labelledby="navbarDropdown">
			
				<a class="dropdown-item" href="<?php echo getURL("admin/allProfiles.php"); ?>">All Profiles</a>
				
				<a class="dropdown-item" href="<?php echo getURL("admin/seeAllCompetitions.php"); ?>">All Competitions</a>
			
				<a class="dropdown-item" href="<?php echo getURL("test/test_create_scores.php"); ?>">Create Score Entry</a>
				
				<a class="dropdown-item" href="<?php echo getURL("test/test_list_scores.php"); ?>">View Score Entries</a>
					
				<a class="dropdown-item" href="<?php echo getURL("test/test_create_pointshistory.php"); ?>">Create Points History Entry</a>
					
				<a class="dropdown-item" href="<?php echo getURL("test/test_list_pointshistory.php"); ?>">View Points History Entries</a>
				
				<a class="dropdown-item" href="<?php echo getURL("test/test_get_winners.php"); ?>">Test Competition Payout</a>
			</div>
		</li>
	<?php endif; ?>
	
</ul>
</nav>
<br>

<div class="container-fluid">