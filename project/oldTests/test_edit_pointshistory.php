<?php require_once(__DIR__ . "/partials/nav.php"); ?>
<?php 
if (!has_role("Admin")) {
    //this will redirect to login and kill the rest of this script (prevent it from executing)
    flash("You don't have permission to access this page");
    die(header("Location: login.php"));
} 
?>

<form method="POST">	
	<label>Points History Entry ID</label>
	<input type="PHEID" name="ID"/>
	
	<label>Reason</label>
	<input type="Reason" name="reason"/>
	
	<input type="submit" name="save" value="Create"/>
</form>

<?php
//we'll put this at the top so both php block have access to it
if(isset($_GET["id"])){
	$id = $_GET["id"];
}
?>

<?php
if(isset($_POST["save"])){
	//TODO add proper validation/checks
	$id = $_POST["ID"];
	
	$reason = $_POST["reason"];
	
	$user = get_user_id();
	$db = getDB();
	
	if (isset($id)) {
		
		$stmt = $db->prepare("UPDATE PointsHistory set reason=:reason WHERE id = :id");
		$r = $stmt->execute([
			":reason"=>$reason,
			":id"=>$id
		]);
		
		if($r){
			flash("Updated Points History entry \"" . $id . "\" with new reason \"" . $reason . "\"");
		}
		else{
			$e = $stmt->errorInfo();
			flash("Error updating: " . var_export($e, true));
		}
	}
	
	else { flash("ID required for updates"); }
}
?>

<?php
//fetching
/*
$result = [];
if(isset($id)){
	$id = $_GET["id"];
	$db = getDB();
	$stmt = $db->prepare("SELECT * FROM Scores where id = :id");
	$r = $stmt->execute([":id"=>$id]);
	$result = $stmt->fetch(PDO::FETCH_ASSOC);
}
*/
?>

<?php require(__DIR__ . "/partials/flash.php"); ?>