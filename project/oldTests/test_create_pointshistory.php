<?php require_once(__DIR__ . "/partials/nav.php"); ?>
<?php 
if (!has_role("Admin")) {
    //this will redirect to login and kill the rest of this script (prevent it from executing)
    flash("You don't have permission to access this page");
    die(header("Location: login.php"));
} 
?>

<form method="POST">
	<label>User ID</label>
	<input type="UserID" name="userID"/>
	
	<label>Points Change</label>
	<input type="PointsChange" name="pointsChange"/>
	
	<label>Reason</label>
	<input type="Reason" name="reason"/>
	
	<input type="submit" name="save" value="Create"/>
</form>

<?php
if(isset($_POST["save"])){
	//TODO add proper validation/checks
	$userID = $_POST["userID"];
	$pointsChange = $_POST["pointsChange"];
	$reason = $_POST["reason"];
	$created = date('Y-m-d H:i:s');//calc
	
	$user = get_user_id();
	$db = getDB();
	$stmt = $db->prepare("INSERT INTO PointsHistory (user_id, points_change, reason, created) VALUES(:user_id, :points_change, :reason, :created)");
	$r = $stmt->execute([
		":user_id"=>$userID,
		":points_change"=>$pointsChange,
		":reason"=>$reason,
		":created"=>$created
	]);
	if($r){
		flash("Created points change entry successfully with id: \"" . $db->lastInsertId() . "\"" );
	}
	else{
		$e = $stmt->errorInfo();
		flash("Error creating: " . var_export($e, true));
	}
}
?>
<?php require(__DIR__ . "/partials/flash.php");