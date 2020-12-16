<?php require_once(__DIR__ . "/../partials/nav.php"); ?>

<?php
if (!has_role("Admin")) {
    //this will redirect to login and kill the rest of this script (prevent it from executing)
    flash("You don't have permission to access this page");
    die(header("Location: /../login.php"));
}
?>

<?php
//we use this to safely get the email to display
$email = "";
if (isset($_SESSION["user"]) && isset($_SESSION["user"]["username"])) {
    $username = $_SESSION["user"]["username"];
}

$db = getDB();

$stmt = $db->prepare("SELECT * FROM Users ORDER BY username ASC");
$stmt->execute();
$allUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<p>
<b>ALL USER PROFILES!</b>
<br>
</p>

<?php foreach($allUsers as $userInfo):?>

<?php getProfileLink($userInfo); ?>
<br>

<?php endforeach;?>

<?php require(__DIR__ . "/../partials/flash.php");