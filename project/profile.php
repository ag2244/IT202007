<?php 

require_once(__DIR__ . "/partials/nav.php"); 

//Redirect to login and kill the rest of this script
if (!is_logged_in()) { die( header("Location: login.php")); }

?>

<?php //Change email, username, password

$db = getDB();

//Save data if form was submitted
if (isset($_POST["saved"])) {
	
	$isValid = true;
	//*******************************
	//CHECK IF NEW EMAIL IS AVAILABLE
	//*******************************
	
	$newEmail = get_email();
	
	if (get_email() != $_POST["email"]) {
		
		//TODO we'll need to check if the email is available
		
		$email = $_POST["email"];
		//Select one user who has the same email as the new email submitted.
		$stmt = $db->prepare("SELECT COUNT(1) as InUse from Users where email = :email");
		$stmt->execute([":email" => $email]);
		$result = $stmt->fetch(PDO::FETCH_ASSOC);
		$inUse = 1; //default it to a failure scenario
		
		if ($result && isset($result["InUse"])) {
			
			try { $inUse = intval($result["InUse"]); }
			
			catch (Exception $e) {}	
		}
		
		if ($inUse > 0) { flash("Email is already in use"); $isValid = false; }
		
		else { $newEmail = $email; }
		
	}
	
	//**********************************
	//CHECK IF NEW USERNAME IS AVAILABLE
	//**********************************
	
	$newUsername = get_username();
	
	if (get_username() != $_POST["username"]) {
		
        $username = $_POST["username"];
		//Select 1 user who has the same username as the new username submitted
        $stmt = $db->prepare("SELECT COUNT(1) as InUse from Users where username = :username");
        $stmt->execute([":username" => $username]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $inUse = 1;//default it to a failure scenario
		
        if ($result && isset($result["InUse"])) {
			
            try { $inUse = intval($result["InUse"]); }
			
            catch (Exception $e) { }
        }
		
        if ($inUse > 0) { flash("Username is already in use"); $isValid = false; }
		
        else { $newUsername = $username; }
    }
	
	//******************************
	//CHECK IF ALL CHANGES ARE VALID
	//******************************
	
	if ($isValid) {
		//Update the User with the same id, changing their username and email
        $stmt = $db->prepare("UPDATE Users set email = :email, username= :username where id = :id");

        $r = $stmt->execute([":email" => $newEmail, ":username" => $newUsername, ":id" => get_user_id()]);
		
        if ($r) { flash("Updated profile<br>"); }
		
        else { flash("Error updating profile<br>"); }
		
        //Check if theres a password reset request
        if (!empty($_POST["password"]) && !empty($_POST["confirm"])) {
			
			$stmt = $db->prepare("SELECT password from Users WHERE id = :id");
			$stmt->execute([":id" => get_user_id()]);
			
			$result = $stmt->fetch(PDO::FETCH_ASSOC);
			
			$password_hash_from_db = $result["password"];
			
			//Check if the reset request is valid
            if (($_POST["password"] == $_POST["confirm"]) && password_verify($_POST["current"], $password_hash_from_db)) {
				
                $password = $_POST["password"];
                $hash = password_hash($password, PASSWORD_BCRYPT);
				
				//Set password for user with the same id to the new password
                $stmt = $db->prepare("UPDATE Users set password = :password where id = :id");
                $r = $stmt->execute([":id" => get_user_id(), ":password" => $hash]);
				
                if ($r) { flash("Reset password"); }
				
                else { flash("Error resetting password"); }
            }
			
			else { flash("Incorrect current password!<br>");}
        }
		
		//Get email and username from at most one user with the same ID (in case anything changed)
        $stmt = $db->prepare("SELECT email, username from Users WHERE id = :id LIMIT 1");
        $stmt->execute([":id" => get_user_id()]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
		
        if ($result) {
			
            $email = $result["email"];
            $username = $result["username"];
            //let's update our session too
            $_SESSION["user"]["email"] = $email;
            $_SESSION["user"]["username"] = $username;
        }
    }
    else {
        //else for $isValid, though don't need to put anything here since the specific failure will output the message
    }
}
?>

<?php  //get user info and scores

if (isset($_GET)) {
	
	if (isset($_GET["id"])) {$userID = $_GET["id"];}
	
}

if (!isset($userID)) {$userID = get_user_id();}

if (isset($userID)) {
	
	echo "<h3><b>UserID " . $userID . "'s Profile!</b></h3>";
	
	$topLifetime = getTopLifetime($userID); 
	$topMonthly = getTopMonthly($userID);
	$topWeekly = getTopWeekly($userID);

	$rankingLife = 1;
	$rankingMonth = 1;
	$rankingWeek = 1;
	}
?>

<br>

<?php if (isset($userID)): ?>
	<p>
		
	  <button class="btn btn-primary" type="button" data-toggle="collapse" data-target="#topLifetime" aria-expanded="false" aria-controls="collapseExample">
		Top Lifetime Scores
	  </button>
	  
	  <button class="btn btn-primary" type="button" data-toggle="collapse" data-target="#topMonthly" aria-expanded="false" aria-controls="collapseExample">
		Top Monthly Scores
	  </button>
	  
	  <button class="btn btn-primary" type="button" data-toggle="collapse" data-target="#topWeekly" aria-expanded="false" aria-controls="collapseExample">
		Top Weekly Scores
	  </button>
	  
	</p>

	<div class="collapse" id="topLifetime">

		<h4>Top Lifetime Scores</h4>
		
		<div class="list-group">
			<?php if (isset($topLifetime)): ?>
				<div class="list-group-item font-weight-bold">
					<div class="row">
						<div class="col">
							Ranking
						</div>
						<div class="col">
							Score
						</div>
						<div class="col">
							Date
						</div>
					</div>
				</div>
				
				<?php foreach ($topLifetime as $score): ?>
					
					<div class="list-group-item">
					
						<div class="row">
							<div class="col">
								<?php safer_echo($rankingLife); $rankingLife++; ?>
							</div>
							
							<div class="col">
								<?php safer_echo($score["score"]); ?>
							</div>
							
							<div class="col">
								<?php safer_echo($score["created"]); ?>
							</div>
							
						</div>
					</div>
					
				<?php endforeach; ?>
				
			<?php else: ?>
				<div class="list-group-item">
					No scores available!
				</div>
			<?php endif; ?>
		</div>

	</div>

	<div class="collapse" id="topMonthly">

		<h4>Top Monthly Scores</h4>
		
		<div class="list-group">
			<?php if (isset($topMonthly)): ?>
				<div class="list-group-item font-weight-bold">
					<div class="row">
						<div class="col">
							Ranking
						</div>
						<div class="col">
							Score
						</div>
						<div class="col">
							Date
						</div>
					</div>
				</div>
				
				<?php foreach ($topMonthly as $score): ?>
					
					<div class="list-group-item">
					
						<div class="row">
							<div class="col">
								<?php safer_echo($rankingMonth); $rankingMonth++; ?>
							</div>
							
							<div class="col">
								<?php safer_echo($score["score"]); ?>
							</div>
							
							<div class="col">
								<?php safer_echo($score["created"]); ?>
							</div>
							
						</div>
					</div>
					
				<?php endforeach; ?>
				
			<?php else: ?>
				<div class="list-group-item">
					No scores available!
				</div>
			<?php endif; ?>
		</div>

	</div>

	<div class="collapse" id="topWeekly">

		<h4>Top Weekly Scores</h4>
		
		<div class="list-group">
			<?php if (isset($topWeekly)): ?>
			
				<div class="list-group-item font-weight-bold">
					<div class="row">
						<div class="col">
							Ranking
						</div>
						<div class="col">
							Score
						</div>
						<div class="col">
							Date
						</div>
					</div>
				</div>
				
				<?php foreach ($topWeekly as $score): ?>
					
					<div class="list-group-item">
					
						<div class="row">
							<div class="col">
								<?php safer_echo($rankingWeek); $rankingWeek++; ?>
							</div>
							
							<div class="col">
								<?php safer_echo($score["score"]); ?>
							</div>
							
							<div class="col">
								<?php safer_echo($score["created"]); ?>
							</div>
							
						</div>
					</div>
					
				<?php endforeach; ?>
				
			<?php else: ?>
				<div class="list-group-item">
					No scores available!
				</div>
				
			<?php endif; ?>
		</div>

	</div>
	
	<br>
	
<?php endif; ?>


<?php if (is_logged_in() && $userID == get_user_id()): 
//If this profile is our own ?>

	<form method="POST">
		<div class="form-group">
			<label for="email">Email</label>
			<input class="form-group" type="email" name="email" value="<?php safer_echo(get_email()); ?>"/>
		</div>
		
		<div class="form-group">
			<label for="username">Username</label>
			<input class="form-group" type="text" maxlength="60" name="username" value="<?php safer_echo(get_username()); ?>"/>
		</div>
		
		<div class="form-group">
			<label for="currpw">Current Password</label>
			<input class="form-group" type="password" name="current"/>
		</div>
		
		<div class="form-group">
			<label for="pw">New Password</label>
			<input class="form-group" type="password" name="password"/>
		</div>
		
		<div class="form-group">
			<label for="cpw">Confirm New Password</label>
			<input class="form-group" type="password" name="confirm"/>
		</div>
		
		<input class="form-control" type="submit" name="saved" value="Save Profile"/>
	</form>

<?php endif; ?>

<!--

<form method="POST">
    <label for="email">Email</label>
    <input type="email" name="email" value="< ?php safer_echo(get_email()); ?>"/>
    <label for="username">Username</label>
    <input type="text" maxlength="60" name="username" value="< ?php safer_echo(get_username()); ?>"/>
	<label for="currpw">Current Password</label>
    <input type="password" name="current"/>
    <label for="pw">Password</label>
    <input type="password" name="password"/>
    <label for="cpw">Confirm Password</label>
    <input type="password" name="confirm"/>
    <input type="submit" name="saved" value="Save Profile"/>
</form>

-->