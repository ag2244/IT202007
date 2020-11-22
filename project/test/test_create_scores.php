<?php require_once(__DIR__ . "/../partials/nav.php"); ?>
<?php 
if (!has_role("Admin")) {
    //this will redirect to login and kill the rest of this script (prevent it from executing)
    flash("You don't have permission to access this page");
    die(header("Location: /../login.php"));
} 
?>

<form method="POST">
	<label>User ID</label>
	<input type="UserID" name="userID"/>
	
	<label>Score</label>
	<input type="Score" name="score"/>
	
	<input type="submit" name="save" value="Create"/>
</form>

<?php
if(isset($_POST["save"])){
	//TODO add proper validation/checks
	$userID = $_POST["userID"];
	$score = $_POST["score"];
	$created = date('Y-m-d H:i:s');//calc
	
	$user = get_user_id();
	$db = getDB();
	$stmt = $db->prepare("INSERT INTO Scores (user_id, score, created) VALUES(:user_id, :score, :created)");
	$r = $stmt->execute([
		":user_id"=>$userID,
		":score"=>$score,
		":created"=>$created
	]);
	if($r){
		flash("Created score entry successfully with id: \"" . $db->lastInsertId() . "\"" );
	}
	else{
		$e = $stmt->errorInfo();
		flash("Error creating: " . var_export($e, true));
	}
}
?>
<?php require(__DIR__ . "/../partials/flash.php");