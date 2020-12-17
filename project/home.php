<?php require_once(__DIR__ . "/partials/nav.php"); ?>
<?php
//we use this to safely get the email to display
$email = "";
if (isset($_SESSION["user"]) && isset($_SESSION["user"]["username"])) {
    $username = $_SESSION["user"]["username"];
}
?>

<p>

<h3>Welcome, 

	<?php 
		if (isset($_SESSION["user"]) && isset($_SESSION["user"]["username"]))
			{echo $username;} 
		else {echo "please log in or register";}
	?>
</h3>

<br>

<a href="<?php echo getURL("spaceRocks.php");?>" >Play Space Rocks!</a>

</p>

<?php  //get user info and scores

$userID = get_user_id();

if (isset($userID)) {
	
	$topLifetime = getTopLifetime($userID); 
	$topMonthly = getTopMonthly($userID);
	$topWeekly = getTopWeekly($userID);

	$rankingLife = 1;
	$rankingMonth = 1;
	$rankingWeek = 1;
	
}
?>

<?php if (isset($_SESSION["user"]) && isset($_SESSION["user"]["username"])): ?>

	<p>
		
	  <button class="btn btn-primary" type="button" data-toggle="collapse" data-target="#topLifetime" aria-expanded="false" aria-controls="collapseExample">
		Top Lifetime Scores
	  </button>
	  
	  <button class="btn btn-primary" type="button" data-toggle="collapse" data-target="#topMonthly" aria-expanded="false" aria-controls="collapseExample">
		Top Monthly Scores
	  </button>
	  
	  <button class="btn btn-primary" type="button" data-toggle="collapse" data-target="#topWeekly" aria-expanded="false" aria-controls="collapseExample">
		Top Weekly Scores
	  </button>
	  
	</p>

	<div class="collapse" id="topLifetime">

		<h4>Top Lifetime Scores</h4>
		
		<div class="list-group">
			<?php if (isset($topLifetime)): ?>
				<div class="list-group-item font-weight-bold">
					<div class="row">
						<div class="col">
							Ranking
						</div>
						<div class="col">
							Score
						</div>
						<div class="col">
							Date
						</div>
					</div>
				</div>
				
				<?php foreach ($topLifetime as $score): ?>
					
					<div class="list-group-item">
					
						<div class="row">
							<div class="col">
								<?php safer_echo($rankingLife); $rankingLife++; ?>
							</div>
							
							<div class="col">
								<?php safer_echo($score["score"]); ?>
							</div>
							
							<div class="col">
								<?php safer_echo($score["created"]); ?>
							</div>
							
						</div>
					</div>
					
				<?php endforeach; ?>
				
			<?php else: ?>
				<div class="list-group-item">
					No scores available!
				</div>
			<?php endif; ?>
		</div>

	</div>

	<div class="collapse" id="topMonthly">

		<h4>Top Monthly Scores</h4>
		
		<div class="list-group">
			<?php if (isset($topMonthly)): ?>
				<div class="list-group-item font-weight-bold">
					<div class="row">
						<div class="col">
							Ranking
						</div>
						<div class="col">
							Score
						</div>
						<div class="col">
							Date
						</div>
					</div>
				</div>
				
				<?php foreach ($topMonthly as $score): ?>
					
					<div class="list-group-item">
					
						<div class="row">
							<div class="col">
								<?php safer_echo($rankingMonth); $rankingMonth++; ?>
							</div>
							
							<div class="col">
								<?php safer_echo($score["score"]); ?>
							</div>
							
							<div class="col">
								<?php safer_echo($score["created"]); ?>
							</div>
							
						</div>
					</div>
					
				<?php endforeach; ?>
				
			<?php else: ?>
				<div class="list-group-item">
					No scores available!
				</div>
			<?php endif; ?>
		</div>

	</div>

	<div class="collapse" id="topWeekly">

		<h4>Top Weekly Scores</h4>
		
		<div class="list-group">
			<?php if (isset($topWeekly)): ?>
			
				<div class="list-group-item font-weight-bold">
					<div class="row">
						<div class="col">
							Ranking
						</div>
						<div class="col">
							Score
						</div>
						<div class="col">
							Date
						</div>
					</div>
				</div>
				
				<?php foreach ($topWeekly as $score): ?>
					
					<div class="list-group-item">
					
						<div class="row">
							<div class="col">
								<?php safer_echo($rankingWeek); $rankingWeek++; ?>
							</div>
							
							<div class="col">
								<?php safer_echo($score["score"]); ?>
							</div>
							
							<div class="col">
								<?php safer_echo($score["created"]); ?>
							</div>
							
						</div>
					</div>
					
				<?php endforeach; ?>
				
			<?php else: ?>
				<div class="list-group-item">
					No scores available!
				</div>
				
			<?php endif; ?>
		</div>

	</div>
	
	<br>
	
<?php endif; ?>

<?php require(__DIR__ . "/partials/flash.php");