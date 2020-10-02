<!-- https://web.njit.edu/~ag2244/samples/sample_login2.php -->

<p>Hi professor!</p>
<form method="POST">
  <label for="email">Email:</label>
  <input type="email" id="email" name="email" required/>
  <label for="p1">Password:</label>
  <input type="password" id="p1" name="password" required/>
  <!-- input of submit has value "Login": meaning the submitted form's data has the value of "Login" -->
  <input type="submit" name="login" value="Login"/> 
</form>

<?php
//Checks the submitted form to see if it exists
if (isset($_POST["login"])) {

	$email = null; $password = null;
	
	if (isset($_POST["email"])) {
		$email = $_POST["email"];
	}
	
	if (isset($_POST["password"])) {
		$password = $_POST["password"];
	}
	
	$isValid = true;
	
	if (!isset($email) || !isset($password)) {
		$isValid = false;
	}
	
	if(!strpos($email, "@")){
		$isValid = false;
		echo "<br>Invalid email<br>";
	}
	
	if ($isValid) {
		
		require_once(__DIR__."/../lib/db.php");
		$db = getDB();
		
		if (isset($db)) {
			//prepared statement means: SELECT the email and password from at most 1 entry where email is equal to :email placeholder.
			$stmt = $db->prepare("SELECT id, email, password from Users WHERE email = :email LIMIT 1");
			
			$params = array(":email" => $email);
			$r = $stmt->execute($params);
			
			echo "db returned: " . var_export($r, true);
			
			$e = $stmt->errorInfo();
			if ($e[0] != "00000"){
				echo "Something went wrong: " . var_export($e, true);
			}
			
			//Fetch results since it's a SELECT command, tell PDO to fetch as an associative array
			$result = $stmt->fetch(PDO::FETCH_ASSOC);
			if ($result && isset($result["password"])) {
				
				$password_hash_from_db = $result["password"];
				if (password_verify($password, $password_hash_from_db)) {
					
					session_start(); //new session!
					
					unset($result["password"]); //for safety
					
					$_SESSION["user"] = $result;
					header ("Location: home.php");
				}
			
				else { echo "<br>INVALID PASSWORD<br>"; }
			
			}
		
			else { echo "<br>INVALID USER<br>"; }
		
		}
		
	}
	
	else{ echo "There was a validation issue";  }
	
}	
?>

<!-- Bottom -->





























<!-- Bottom 2 Electric Boogaloo -->