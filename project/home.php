<?php require_once(__DIR__ . "/partials/nav.php"); ?>
<?php
//we use this to safely get the email to display
$email = "";
if (isset($_SESSION["user"]) && isset($_SESSION["user"]["username"])) {
    $username = $_SESSION["user"]["username"];
}
?>
<p>Welcome, <?php 
	if (isset($_SESSION["user"]) && isset($_SESSION["user"]["username"]))
		{echo $username;} 
	else {echo "please log in or register";}
?></p

<?php require(__DIR__ . "/partials/flash.php");