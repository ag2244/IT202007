<p>Run me in the browser from your server to try</p>
<form method="POST">
	<!-- All of these are inline. -->
	<label for="email">Email:</label>
	<input type="email" id="email" name="email" required/>
	<!-- Input type is password, the id is the specific kind of password (password 1 or 2) and the name is our human-readable name.-->
	<label for="p1">Password:</label>
	<input type="password" id="p1" name="password" required/>
	<label for="p2">Confirm Password:</label>
	<input type="password" id="p2" name="confirm" required/>
	<input type="submit" name="register" value="Register"/>
</form>

<?php
if(isset($_POST["register"])){
	$email = null;
	$password = null;
	$confirm = null;
	
	//if email has been added to $_POST, set $email
	if(isset($_POST["email"])){ 
		$email = $_POST["email"];
	}
	
	//if password has been added to $_POST, set $password
	if(isset($_POST["password"])){ 
		$password = $_POST["password"];
	}
	
	//if confirm has been added to $_POST, set $confirm
	if(isset($_POST["confirm"])){ 
		$confirm = $_POST["confirm"];
	}
		$isValid = true;
	//check if passwords match on the server side
	if($password == $confirm){
		echo "Passwords match <br>"; 
	}
	else{
		echo "Passwords don't match<br>";
		$isValid = false;
	}
	
	//If any of these are not set
	if(!isset($email) || !isset($password) || !isset($confirm)){
		$isValid = false; 
	}
	//TODO other validation as desired, remember this is the last line of defense
	if($isValid){
		$hash = password_hash($password, PASSWORD_BCRYPT);
		require_once("db.php");
		$db = getDB();
		if(isset($db)){
			//here we'll use placeholders to let PDO map and sanitize our data
			$stmt = $db->prepare("INSERT INTO Users(email, password) VALUES(:email, :password)");
			//here's the data map for the parameter to data
			$params = array(":email"=>$email, ":password"=>$hash);
			//executes stmt, replacing the placeholders with their values as keys in $params.
			$r = $stmt->execute($params);
			//let's just see what's returned
			echo "db returned: " . var_export($r, true);
			$e = $stmt->errorInfo();
			if($e[0] == "00000"){
				echo "<br>Welcome, $email! You successfully registered, please login.";
			}
			else{
				echo "uh oh something went wrong: " . var_export($e, true);
			}
		}
	}
	else{
		echo "There was a validation issue"; 
	}
}
?>