<?php 
require_once(__DIR__ . "/../partials/nav.php"); 

if (!is_logged_in()) { die( header("Location: /../login.php")); }
?>

<?php

$db = getDB();
$id = $_GET["comp"];
	
$stmt = $db->prepare("SELECT * FROM Competitions WHERE id = :id LIMIT 1");
$stmt->execute([
	":id" => $id
	]);
	
$compInfo = $stmt->fetch(PDO::FETCH_ASSOC);

?>

<form method="POST">

	<div class="form-group">
		<label>Competition Name</label>
		<br>
		<input class="form-group" type="name" name="name" value = "<?php echo $compInfo["name"] ?>"/>
	</div>

	<div class="form-group">
		<label>New Competition Duration (Days)</label>
		<br>
		<input class="form-group" type="duration" name="duration" value = "<?php echo $compInfo["duration"] ?>"/>
	</div>

	<div class="form-group">
		<label>Minimum Score to Qualify</label>
		<br>
		<input class="form-group" type="min_score" name="min_score" value = "<?php echo $compInfo["min_score"] ?>"/>
	</div>

	<div class="form-group">
		<label>First Place Reward Percentage (Out of 100)</label>
		<br>
		<input class="form-group" type="first_place_per" name="first_place_per" value = "<?php echo $compInfo["first_place_per"] ?>"/>
	</div>

	<div class="form-group">
		<label>Second Place Reward Percentage (Out of 100)</label>
		<br>
		<input class="form-group" type="second_place_per" name="second_place_per" value = "<?php echo $compInfo["second_place_per"] ?>"/>
	</div>

	<div class="form-group">
		<label>Third Place Reward Percentage (Out of 100)</label>
		<br>
		<input class="form-group" type="third_place_per" name="third_place_per" value = "<?php echo $compInfo["third_place_per"] ?>"/>
	</div>

	<div class="form-group">
		<label>Entry Fee (0 for Free)</label>
		<br>
		<input class="form-group" type="fee" name="fee" value = "<?php echo $compInfo["fee"] ?>"/>
	</div>
	
	<div class="form-group">
		<label>Reward Pool
		<br>
		<input class="form-group" type="reward" name="reward" value = "<?php echo $compInfo["reward"] ?>"/>
	</div>
	
	<input class="form-control" type="submit" name="newComp" value="Create Competition"/>
</form>


<?php

if(isset($_POST["newComp"])){
	//TODO add proper validation/checks
	$name = $_POST["name"];
	$duration = $_POST["duration"];
	$expires = date('Y-m-d H:i:s', strtotime($duration . " days"));
	$min_score = $_POST["min_score"];
	$first_place_per = $_POST["first_place_per"];
	$second_place_per = $_POST["second_place_per"];
	$third_place_per = $_POST["third_place_per"];
	$fee = $_POST["fee"];
	
	$reward = (int)$_POST["reward"];
    if ($reward <= 0) {
        $reward = 0;
    }
    $reward++;
	
	if (!(abs((float)$first_place_per + (float)$second_place_per + (float)$third_place_per - 100.0) < 0.00001)) { 
		flash("Error creating Competition: First, Second, Third place percentages must add up to 100.");
	}
	
	else {
		$user = get_user_id();
		$db = getDB();
		
		$stmt = $db->prepare("UPDATE Competitions SET
								name = :name,
								duration = :duration,
								expires = :expires,
								reward = :reward,
								min_score = :min_score,
								first_place_per = :first_place_per,
								second_place_per = :second_place_per,
								third_place_per = :third_place_per,
								fee = :fee
								WHERE id = :id
								");
			
		$rComp = $stmt->execute([
			":name"=>$name,
			":duration"=>$duration,
			":expires"=>$expires,
			":reward"=>$reward,
			":min_score"=>$min_score,
			":first_place_per"=>$first_place_per,
			":second_place_per"=>$second_place_per,
			":third_place_per"=>$third_place_per,
			":fee"=>$fee,
			":id" => $id
		]);
		
		if($rComp){  flash("Edited competition successfully");  }
		
		else{
			$e = $stmt->errorInfo();
			flash("Error creating: " . var_export($e, true));
		}
	}

}

?>

<?php require(__DIR__ . "/../partials/flash.php");