<?php
session_start();
?>
<ul>
  <li><a href="home.php">Home</a></li>
  <li><a href="login.php">Login</a></li>
  <li><a href="registration.php">Register</a></li>
  <li><a href="logout.php">Logout</a></li>
</ul>
<?php
//we use this to safely get the email to display
$email = "";
if(isset($_SESSION["user"]) && isset($_SESSION["user"]["email"])){
 $email = $_SESSION["user"]["email"]; 
}
?>
<p>Welcome<?php if ($email) { echo", $email"; } else {echo ", Stranger!";}?></p>