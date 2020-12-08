<?php require_once(__DIR__ . "/../partials/nav.php"); ?>

<?php
	try {
		var_dump(endCompetition("9"));
	}
	
	catch (exception $e) {
		var_dump($e);
	}
?>

<?php require(__DIR__ . "/../partials/flash.php"); ?>