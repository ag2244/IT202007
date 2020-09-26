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
		//for password security we'll generate a hash that'll be saved to the DB instead of the raw password
		//for this sample we'll show it instead
		$hash = password_hash($password, PASSWORD_BCRYPT);
		echo "<br>Our hash: $hash<br>";
		echo "User registered (not really since we don't have a database setup yet)"; 
	}
	else{
		echo "There was a validation issue"; 
	}
}
?>