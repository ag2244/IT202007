<?php require_once(__DIR__ . "/partials/nav.php"); ?>
<?php
if (!is_logged_in()) {
	flash("You don't have permission to access this page");
	die(header("Location: login.php"));
}
?>

<?php


$db = getDB();

if (isset($_POST["join"])) {

	$lifetimePoints = getLifetimePoints();
	
	$stmt = $db->prepare("SELECT fee FROM Competitions WHERE id = :id && expires > current_timestamp && paid_out = 0");
	$r = $stmt->execute([":id" => $_POST["compID"]]);
	
	if ($r) {
		
		$result = $stmt->fetch(PDO::FETCH_ASSOC);
		
		if ($result) {
		
			$fee = (int)$result["fee"];
			
			if ($lifetimePoints >= $fee) {
				
				$stmt = $db->prepare("INSERT INTO CompetitionParticipants (comp_id, user_id) VALUES(:compID, :user_id)");
				$r = $stmt->execute([":compID" => $_POST["compID"], ":user_id" => get_user_id()]);
				
				if ($r) {
                    flash("Successfully joined competition!", "success");
                    die(header("Location: #"));
                }
			
				else {flash("There was a problem joining the competition: " . var_export($stmt->errorInfo(), true), "danger");}
				
			}
			
			else { flash("You can't afford to join this competition, try again later", "warning"); }
		}
		
		 else { flash("You can't afford to join this competition, try again later", "warning"); } 
	}
	
	else { flash("Competition is unavailable", "warning"); }
}

$stmt = $db->prepare("SELECT comps.*, compParts.user_id as reg 
	FROM Competitions comps 
	LEFT JOIN 
		(SELECT * FROM CompetitionParticipants where user_id = :user_id) 
		as compParts on comps.id = compParts.comp_id 
	WHERE comps.expires > current_timestamp AND paid_out = 0 
	ORDER BY expires ASC");
$r = $stmt->execute([":user_id" => get_user_id()]);

if ($r) { $results = $stmt->fetchAll(PDO::FETCH_ASSOC); }

else { flash("There was a problem looking up competitions: " . var_export($stmt->errorInfo(), true), "danger"); }

?>

    <div class="container-fluid">
        <h3>Competitions</h3>
        <div class="list-group">
            <?php if (isset($results) && count($results)): ?>
                <div class="list-group-item font-weight-bold">
                    <div class="row">
                        <div class="col">
                            Name
                        </div>
                        <div class="col">
                            Participants
                        </div>
                        <div class="col">
                            Required Score
                        </div>
                        <div class="col">
                            Reward
                        </div>
                        <div class="col">
                            Expires
                        </div>
                        <div class="col">
                            Actions
                        </div>
                    </div>
                </div>
				
                <?php foreach ($results as $r): ?>
					
                    <div class="list-group-item">
					
                        <div class="row">
                            <div class="col">
                                <?php safer_echo($r["name"]); ?>
                            </div>
							
                            <div class="col">
                                <?php safer_echo($r["participants"]); ?>
                            </div>
							
                            <div class="col">
                                <?php safer_echo($r["min_score"]); ?>
                            </div>
							
                            <div class="col">
                                <?php safer_echo($r["reward"]); ?>
                                <!--TODO show payout-->
                            </div>
							
                            <div class="col">
                                <?php safer_echo($r["expires"]); ?>
                            </div>
							
                            <div class="col">
                                <?php if ($r["reg"] != get_user_id()): ?>
                                    <form method="POST">
									
                                        <input type="hidden" name="compID" value="<?php safer_echo($r["id"]); ?>"/>
                                        <input type="submit" name="join" class="btn btn-primary"
                                               value="Join (Cost: <?php safer_echo($r["fee"]); ?>)"/>
                                    </form>
                                <?php else: ?>
                                    Already Registered
                                <?php endif; ?>
                            </div>
							
                        </div>
                    </div>
					
                <?php endforeach; ?>
            <?php else: ?>
                <div class="list-group-item">
                    No competitions available right now
                </div>
            <?php endif; ?>
        </div>
    </div>

<?php require(__DIR__ . "/partials/flash.php");
































