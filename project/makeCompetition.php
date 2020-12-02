<?php 
require_once(__DIR__ . "/partials/nav.php"); 

if (!is_logged_in()) { die( header("Location: login.php")); }
?>

<form method="POST">
	<label>Competition Name</label>
	<input type="name" name="name"/>
	
	<label>Competition Duration (Days)</label>
	<input type="duration" name="duration"/>
	
	<label>Overall Competition Reward</label>
	<input type="reward" name="reward"/>
	
	<label>Minimum Score to Qualify</label>
	<input type="min_score" name="min_score"/>
	
	<label>First Place Reward Percentage (Out of 100)</label>
	<input type="first_place_per" name="first_place_per"/>
	
	<label>Second Place Reward Percentage (Out of 100)</label>
	<input type="second_place_per" name="second_place_per"/>
	
	<label>Third Place Reward Percentage (Out of 100)</label>
	<input type="third_place_per" name="third_place_per"/>
	
	<label>Entry Fee</label>
	<input type="fee" name="fee"/>
	
	<input type="submit" name="newComp" value="Create Competition"/>
</form>


<?php echo "test";

if(isset($_POST["newComp"])){
	//TODO add proper validation/checks
	$name = $_POST["name"];
	$created = date('Y-m-d H:i:s');//calc
	$duration = $_POST["duration"];
	$expires = date('Y-m-d H:i:s', strtotime($duration . " days"));
	$reward = $_POST["reward"];
	$min_score = $_POST["min_score"];
	$first_place_per = $_POST["first_place_per"];
	$second_place_per = $_POST["second_place_per"];
	$third_place_per = $_POST["third_place_per"];
	$fee = $_POST["fee"];
	
	if (abs((float)$first_place_per + (float)$second_place_per + (float)$third_place_per - 100.0) < 0.00001) {
	
		$user = get_user_id();
		$db = getDB();
		$stmt = $db->prepare("INSERT INTO Competitions 
			(name, created, duration, expires, reward, min_score, first_place_per, second_place_per, third_place_per, fee) 
			VALUES
			(:name, :created, :duration, :expires, :reward, :min_score, :first_place_per, :second_place_per, :third_place_per, :fee)");
			
		$r = $stmt->execute([
			":name"=>$name,
			":created"=>$created,
			":duration"=>$duration,
			":expires"=>$expires,
			":reward"=>$reward,
			":min_score"=>$min_score,
			":first_place_per"=>$first_place_per,
			":second_place_per"=>$second_place_per,
			":third_place_per"=>$third_place_per,
			":fee"=>$fee
		]);
		
		if($r){
			flash("Created competition successfully with id: \"" . $db->lastInsertId() . "\"" );
		}
		
		else{
			$e = $stmt->errorInfo();
			flash("Error creating: " . var_export($e, true));
		}
	
	}
	
	else { flash("Error creating Competition: First, Second, Third place percentages must add up to 100."); }
}

?>

<?php require(__DIR__ . "/partials/flash.php");