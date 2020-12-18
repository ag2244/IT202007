<?php require_once(__DIR__ . "/../partials/nav.php"); ?>
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

if (isset($_POST["restart"])) {
	
	$stmt = $db->prepare("UPDATE Competitions SET duration = :newDuration, expires = :newExpires WHERE id = :id");
	$r = $stmt->execute([
		":newDuration"=>$_POST["extraDuration"], 
		":newExpires"=>date('Y-m-d H:i:s', strtotime($_POST["extraDuration"] . " days")), 
		":id" => $_POST["compID"]
	]);
	
}


$stmt = $db->prepare("SELECT comps.*, compParts.user_id as reg 
	FROM Competitions comps 
	LEFT JOIN 
		(SELECT * FROM CompetitionParticipants where user_id = :user_id) 
		as compParts on comps.id = compParts.comp_id 
	WHERE paid_out = 0 
	ORDER BY expires ASC LIMIT :offset, :count");

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
			
				<!-- Column Names -->
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
								<div class="col">
								<a class="btn btn-primary" href="<?php echo getURL("admin/editCompetition.php"); ?>
								?comp=
								<?php safer_echo($r["id"]); ?>
								" role="button">Edit Competition</a>
                            </div>
									
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
                    No competitions available right now
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

<?php require(__DIR__ . "/../partials/flash.php");



















