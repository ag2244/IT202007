<!-- (LINK) -->

<!-- Navigation -->
<?php require_once(__DIR__ . "/partials/nav.php"); ?>

<form method="POST">
  <label for="email">Email:</label>
  <input type="email" id="email" name="email" />
  
  <label for="username">Username:</label>
  <input type="username" id="username" name="username" />
  
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
		
		require_once("lib/db.php");
		$db = getDB();
		
		if (isset($db)) {
			//prepared statement means: SELECT the email and password from at most 1 entry where email is equal to :email placeholder.
			$stmt = $db->prepare("SELECT id, email, username, password from Users WHERE email = :email LIMIT 1");
			
			$params = array(":email" => $email);
			$r = $stmt->execute($params);
			
			flash("db returned: " . var_export($r, true));
			
			$e = $stmt->errorInfo();
			if ($e[0] != "00000"){
				//echo "Something went wrong: " . var_export($e, true);
				flash("Something went wrong!");
			}
			
			//Fetch results since it's a SELECT command, tell PDO to fetch as an associative array
			$result = $stmt->fetch(PDO::FETCH_ASSOC);
			
			if ($result && isset($result["password"])) {
				
				$password_hash_from_db = $result["password"];
				if (password_verify($password, $password_hash_from_db)) {
					
					$stmt = $db->prepare("
					SELECT Roles.name FROM Roles JOIN UserRoles on Roles.id = UserRoles.role_id where UserRoles.user_id = :user_id and Roles.is_active = 1 and UserRoles.is_active = 1");
					$stmt->execute([":user_id" => $result["id"]]);
					$roles = $stmt->fetchAll(PDO::FETCH_ASSOC);

					unset($result["password"]);//remove password so we don't leak it beyond this page
					
					//let's create a session for our user based on the other data we pulled from the table
					$_SESSION["user"] = $result;//we can save the entire result array since we removed password
					if ($roles) {
						$_SESSION["user"]["roles"] = $roles;
					}
					else {
						$_SESSION["user"]["roles"] = [];
					}
					//on successful login let's serve-side redirect the user to the home page.
					header("Location: home.php");
				}
			
				else { flash("<br>Invalid Password!<br>"); }
			
			}
		
			else { flash("<br>Account does not exist!<br>"); }
		
		}
		
	}
	
	else{ flash("There was a validation issue!");  }
	
}	
?>

<?php require(__DIR__ . "/partials/flash.php"); ?>

<!-- Bottom -->





























<!-- Bottom 2 Electric Boogaloo -->