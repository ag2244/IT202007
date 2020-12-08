<?php 
require_once(__DIR__ . "/partials/nav.php"); 

if (!is_logged_in()) { die( header("Location: login.php")); }
?>

<form method="POST">

	<div class="form-group">
		<label>Competition Name</label>
		<br>
		<input class="form-group" type="name" name="name"/>
	</div>

	<div class="form-group">
		<label>Competition Duration (Days)</label>
		<br>
		<input class="form-group" type="duration" name="duration"/>
	</div>

	<div class="form-group">
		<label>Minimum Score to Qualify</label>
		<br>
		<input class="form-group" type="min_score" name="min_score"/>
	</div>

	<div class="form-group">
		<label>First Place Reward Percentage (Out of 100)</label>
		<br>
		<input class="form-group" type="first_place_per" name="first_place_per"/>
	</div>

	<div class="form-group">
		<label>Second Place Reward Percentage (Out of 100)</label>
		<br>
		<input class="form-group" type="second_place_per" name="second_place_per"/>
	</div>

	<div class="form-group">
		<label>Third Place Reward Percentage (Out of 100)</label>
		<br>
		<input class="form-group" type="third_place_per" name="third_place_per"/>
	</div>

	<div class="form-group">
		<label>Entry Fee (0 for Free)</label>
		<br>
		<input class="form-group" type="fee" name="fee"/>
	</div>
	
	<div class="form-group">
		<label>Starting Reward Pool
		<br>
		Creation Cost (Points) = Starting Pool + 1 (<?php echo getLifetimePoints(); ?> Lifetime Points)</label>
		<br>
		<input class="form-group" type="cost" name="cost"/>
	</div>
	
	<input class="form-control" type="submit" name="newComp" value="Create Competition"/>
</form>


<?php

if(isset($_POST["newComp"])){
	//TODO add proper validation/checks
	$name = $_POST["name"];
	$created = date('Y-m-d H:i:s');//calc
	$duration = $_POST["duration"];
	$expires = date('Y-m-d H:i:s', strtotime($duration . " days"));
	$reward = 1;
	$min_score = $_POST["min_score"];
	$first_place_per = $_POST["first_place_per"];
	$second_place_per = $_POST["second_place_per"];
	$third_place_per = $_POST["third_place_per"];
	$fee = $_POST["fee"];
	
	$cost = (int)$_POST["cost"];
    if ($cost <= 0) {
        $cost = 0;
    }
    $cost++;
	
	if (getLifetimePoints() < $cost) { 
		flash("Error creating Competition: Cannot afford to make the competition!");
	}
	
	else if (!(abs((float)$first_place_per + (float)$second_place_per + (float)$third_place_per - 100.0) < 0.00001)) { 
		flash("Error creating Competition: First, Second, Third place percentages must add up to 100.");
	}
	
	else {
		$user = get_user_id();
		$db = getDB();
		
		$stmt = $db->prepare("INSERT INTO Competitions 
			(name, created, duration, expires, cost, reward, min_score, first_place_per, second_place_per, third_place_per, fee, user_id) 
			VALUES
			(:name, :created, :duration, :expires, :cost, :reward, :min_score, :first_place_per, :second_place_per, :third_place_per, :fee, :user_id)");
			
		$rNewComp = $stmt->execute([
			":name"=>$name,
			":created"=>$created,
			":duration"=>$duration,
			":expires"=>$expires,
			":cost"=>$cost,
			":reward"=>$reward,
			":min_score"=>$min_score,
			":first_place_per"=>$first_place_per,
			":second_place_per"=>$second_place_per,
			":third_place_per"=>$third_place_per,
			":fee"=>$fee,
			":user_id"=>$user
		]);
		
		if($rNewComp){ 
		
			flash("Created competition successfully with id: \"" . $db->lastInsertId() . "\"" ); 
		
			$stmt = $db->prepare("UPDATE Users SET lifetimePoints = :newVal WHERE id = :id");
				
			$rLifetimePoints = $stmt->execute([ ":id"=>$user, ":newVal"=>getLifetimePoints() - $cost ]);
			
			$stmt = $db->prepare("INSERT INTO PointsHistory (user_id, points_change, reason) VALUES(:user_id, :points_change, :reason)");

			$rPointsHistory = $stmt->execute([
				":user_id"=>$user,
				":points_change"=>$cost,
				":reason"=>"Created Competition with ID " . (string)$db->lastInsertId()
			]);
			
		}
		
		else{
			$e = $stmt->errorInfo();
			flash("Error creating: " . var_export($e, true));
		}
	}

}

?>

<?php require(__DIR__ . "/partials/flash.php");