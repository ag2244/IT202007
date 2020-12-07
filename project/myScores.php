<?php require_once(__DIR__ . "/partials/nav.php"); ?>

<?php
if (!is_logged_in()) {
    flash("You must be logged in to access this page");
    die(header("Location: login.php"));
}

/*
IMPORTANT!!!!
CHANGE $page TO $pageNum
CHANGE $per_page TO $pageLen
*/

$page = 1;
$per_page = 10;

// If we know the desired page number
if(isset($_GET["page"])){
    try {
        $page = (int)$_GET["page"];
    }
    catch(Exception $e){

    }
}

$db = getDB();
$stmt = $db->prepare("SELECT count(*) AS total FROM Scores WHERE user_id = :id");
$stmt->execute([":id"=>get_user_id()]);
$result = $stmt->fetch(PDO::FETCH_ASSOC);

$total = 0;
if($result){
	$total = (int)$result["total"];
}

$total_pages = ceil($total / $per_page);
$offset = ($page-1) * $per_page; //List offset for list of score entries

$stmt = $db->prepare("SELECT * FROM Scores WHERE user_id = :id ORDER BY created DESC LIMIT :offset, :count");
//LIMIT basically means the slice of the query's results we want
//need to use bindValue to tell PDO to create these as ints
//otherwise it fails when being converted to strings (the default behavior)
$stmt->bindValue(":offset", $offset, PDO::PARAM_INT);
$stmt->bindValue(":count", $per_page, PDO::PARAM_INT);
$stmt->bindValue(":id", get_user_id());
$stmt->execute();
$e = $stmt->errorInfo();
if($e[0] != "00000"){
    flash(var_export($e, true), "alert");
}
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

    <div class="container-fluid">
    <h3>My Scores</h3>
    <div class="row">
    <div class="card-group">
	
<?php if($results && count($results) > 0):?>

    <?php foreach($results as $r):?>
        <div class="col-auto mb-3">
            <div class="card" style="width: 18rem;">
                <div class="card-body">
				
                    <div class="card-title">
						Score: <?php safer_echo($r["score"]);?>
                    </div>
					
                    <div class="card-text">
                        <div>Score Date: <?php safer_echo($r["created"]); ?></div>
                    </div>
					
                    <div class="card-footer">
                        <div>Score Entry ID: <?php safer_echo($r["id"]); ?></div>
                    </div>
					
                </div>
            </div>
        </div>
    <?php endforeach;?>

<?php else:?>
<div class="col-auto">
    <div class="card">
       You don't have any Scores.
    </div>
</div>
<?php endif;?>

    </div>
    </div>
        <nav aria-label="My Scores">
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
    </div> 
<?php require(__DIR__ . "/partials/flash.php");