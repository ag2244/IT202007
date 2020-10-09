<?php require_once(__DIR__ . "/partials/nav.php"); ?>

<?php
//starts/loads a session, basically tells php to do its magic
session_start();
// remove all session variables
session_unset();
// destroy the session
session_destroy();
echo "You're logged out (proof by dumping the session)<br>";

echo "Below is the session data (you'll see that there is none!):";
echo "<pre>" . var_export($_SESSION, true) . "</pre>";
?>