<?php require_once(__DIR__ . "/partials/nav.php"); ?>
<?php
if (!is_logged_in()) {
	flash("You don't have permission to access this page");
	die(header("Location: /../login.php"));
}
?>

<?php //Page info
$page = 1;
$per_page = 10;

if(isset($_GET["page"])){
	try { $page = (int)$_GET["page"]; }
	catch(Exception $e){ }
}

//Get number of entries
$db = getDB();
$stmt = $db->prepare("SELECT count(*) AS total FROM Competitions WHERE comps.expires > current_timestamp AND paid_out = 0");
$stmt->execute([":id"=>get_user_id()]);
$result = $stmt->fetch(PDO::FETCH_ASSOC);

//Set total number of pages
$total = 0;
if($result){ $total = (int)$result["total"]; }
$total_pages = ceil($total / $per_page);
$offset = ($page-1) * $per_page;

?>

<?php //Get Competitions


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
				$rParticipants = $stmt->execute([":compID" => $_POST["compID"], ":user_id" => get_user_id()]);
				
				$stmt = $db->prepare("UPDATE Competitions SET participants = participants + 1 WHERE id = :compID");
				$rCompetitions = $stmt->execute([":compID" => $_POST["compID"]]);
				
				if ($rParticipants && $rCompetitions) {
                    flash("Successfully joined competition!", "success");
					
					$stmt = $db->prepare("UPDATE Users SET lifetimePoints = :newVal WHERE id = :id");
				
					$rLifetimePoints = $stmt->execute([ ":id"=>$user, ":newVal"=>getLifetimePoints() - $fee]);
					
					$stmt = $db->prepare("INSERT INTO PointsHistory (user_id, points_change, reason) VALUES(:user_id, :points_change, :reason)");
					
					$rPointsHistory = $stmt->execute([
						":user_id"=>$user,
						":points_change"=>$cost,
						":reason"=>"Joined Competition with ID " . (string)$db->lastInsertId()
					]);
					
					$stmt = $db->prepare("UPDATE Competitions SET reward = reward + :newAdd WHERE id = :compID");
				
					$stmt->execute([":compID" => $_POST["compID"], ":newAdd" => max(1, floor($fee/2))]);
					
                    die(header("Location: #")); 
                }
			
				else {flash("There was a problem joining the competition: " . var_export($stmt->errorInfo(), true), "danger");}
				
			}
			
			else { flash("You can't afford to join this competition, try again later", "warning"); }
		}
		
		 else { flash("You can't afford to join this competition, try again later", "warning"); }
	}
	
	else { flash("Competition is unavailable"); }
}


$stmt = $db->prepare("SELECT comps.*, compParts.user_id as reg 
	FROM Competitions comps 
	INNER JOIN 
		(SELECT * FROM CompetitionParticipants where user_id = :user_id) 
		as compParts on comps.id = compParts.comp_id 
	ORDER BY expires DESC LIMIT :offset, :count");

$stmt->bindValue(":offset", $offset, PDO::PARAM_INT);
$stmt->bindValue(":count", $per_page, PDO::PARAM_INT);
$stmt->bindValue(":user_id", get_user_id());
$stmt->execute();

$e = $stmt->errorInfo();
if($e[0] != "00000"){
    flash(var_export($e, true), "alert");
}
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
						Top Ten Leaderboard
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
								<?php if($r["expires"] < date('Y-m-d H:i:s')) echo " <b>(EXPIRED!)</b>"?>
                            </div>
							
							<div class="col">
								<a class="btn btn-primary" href="<?php echo getURL("leaderboard.php"); ?>
								?comp=
								<?php safer_echo($r["id"]); ?>
								" role="button">See Leaderboard</a>
                            </div>
							
                        </div>
                    </div>
					
                <?php endforeach; ?>
            <?php else: ?>
                <div class="list-group-item">
                    No competitions in your history!
                </div>
            <?php endif; ?>
        </div>
		
		<p>
		<!-- Pages -->
		<nav aria-label="Competitions">
            <ul class="pagination justify-content-center">
                <li class="page-item <?php echo ($page-1) < 1?"disabled":"";?>">
                    <a class="page-link" href="?page=<?php echo $page-1;?>" tabindex="-1">Previous</a>
                </li>
				
                <?php for($i = 0; $i < $total_pages; $i++):?>
				
					<li class="page-item <?php echo ($page-1) == $i?"active":"";?>"><a class="page-link" href="?page=<?php echo ($i+1);?>"><?php echo ($i+1);?></a></li>
				
                <?php endfor; ?>
				
                <li class="page-item <?php echo ($page+1) >= $total_pages?"disabled":"";?>">
                    <a class="page-link" href="?page=<?php echo $page+1;?>">Next</a>
                </li>
            </ul>
        </nav>
		</p>
		
    </div>

<?php require(__DIR__ . "/partials/flash.php");



















