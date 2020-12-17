<?php require_once(__DIR__ . "/partials/nav.php"); ?>
<?php
//we use this to safely get the email to display

	$db = getDB();
	$compID = $_GET["comp"];
	
	//Competition Info
	
	$stmt = $db->prepare("SELECT * FROM Competitions WHERE id = :compID LIMIT 1");
	
	$stmt->execute([ ":compID"=>$compID ]);
	
	$compInfo = $stmt->fetch(PDO::FETCH_ASSOC);
	
	//Competition Participants
	
	$stmt = $db->prepare("SELECT username, CompetitionParticipants.created FROM CompetitionParticipants INNER JOIN Users ON CompetitionParticipants.user_id = Users.id");
	
	$stmt->execute([ ":compID"=>$compID ]);
	
	$participants = $stmt->fetchAll(PDO::FETCH_ASSOC);
	
	//var_dump($participants);
	
	//Valid Scores
	
	$stmt = $db->prepare(
		"SELECT * FROM Scores INNER JOIN 
		
			(SELECT username, user_id, CompetitionParticipants.created 
			FROM CompetitionParticipants INNER JOIN Users 
			ON CompetitionParticipants.user_id = Users.id AND CompetitionParticipants.comp_id = :comp_id)
			
			AS Participants
		ON Scores.user_id = Participants.user_id
		
		WHERE 
			Scores.created > Participants.created
			AND
			Participants.created < :endDate
			AND
			Scores.score > :minimumScore
			
		ORDER BY score DESC LIMIT 10");
		
	$stmt->execute([ ":comp_id"=>$compID, ":endDate"=>$compInfo["expires"], ":minimumScore"=>$compInfo["min_score"] ]);
	
	$allValidScores = $stmt->fetchAll(PDO::FETCH_ASSOC);
	
	//echo '<pre>'; var_dump($allScores); echo '</pre>';
	
	$ranking = 1;

?>

<p>

<a href="<?php echo getURL("spaceRocks.php");?>" >Play Space Rocks!</a>

</p>

	<h4>Top Ten Scores</h4>
	
	<div class="list-group">
		<?php if (isset($allValidScores)): ?>
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
					<div class="col">
						User
					</div>
				</div>
			</div>
			
			<?php foreach ($allValidScores as $score): ?>
				
				<div class="list-group-item">
				
					<div class="row">
						<div class="col">
							<?php safer_echo($ranking); $ranking++; ?>
						</div>
						
						<div class="col">
							<?php safer_echo($score["score"]); ?>
						</div>
						
						<div class="col">
							<?php safer_echo($score["created"]); ?>
						</div>
						
						<div class="col">
							<?php safer_echo($score["username"]); ?>
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


<?php require(__DIR__ . "/partials/flash.php");






























