
<?php
//starts/loads a session, basically tells php to do its magic
session_start();
// remove all session variables
session_unset();
// destroy the session
session_destroy();
?>

<?php require_once(__DIR__ . "/partials/nav.php"); ?>

<?php
flash("Successfully logged out!<br>");
die(header("Location: login.php"));
?>