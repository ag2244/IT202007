<?php
//since API is 100% server, we won't include navbar or flash
require_once(__DIR__ . "/../lib/helpers.php");
if (!is_logged_in()) {
    die(header(':', true, 403));
}

/*
$testing = false;
if (isset($_GET["test"])) {
    $testing = true;
}
*/

//INSERT NEW SCORE INTO SCORES TABLE
$userID = get_user_id();
$score = $_POST["score"];
$created = date('Y-m-d H:i:s');

$db = getDB();
$stmt = $db->prepare("INSERT INTO Scores (user_id, score, created) VALUES(:user_id, :score, :created)");

$rScores = $stmt->execute([
	":user_id"=>$userID,
	":score"=>$score,
	":created"=>$created
]);

$stmt = $db->prepare("SELECT lifetimePoints FROM Users WHERE id = :id");
$stmt->execute([ ":id"=>$userID ]);
$previousPoints = $stmt->fetch(PDO::FETCH_ASSOC);

//ADD SCORE TO USER LIFETIME POINTS
$stmt = $db->prepare("UPDATE Users SET lifetimePoints = :score + :previousPoints WHERE id = :id");

$rUsers = $stmt->execute([
	":id"=>$userID,
	":score"=>$score,
	":previousPoints"=>$previousPoints["lifetimePoints"]
]);

$stmt->fetchAll(PDO::FETCH_ASSOC);

//INSERT INFO ABOUT NEW SCORE INTO POINTSHISTORY
$pointsChange = $_POST["score"];
$reason = "New Score from game: " . $_POST["gameName"];

$stmt = $db->prepare("INSERT INTO PointsHistory (user_id, points_change, reason, created) VALUES(:user_id, :points_change, :reason, :created)");

$rPointsHistory = $stmt->execute([
	":user_id"=>$userID,
	":points_change"=>$score,
	":reason"=>$reason,
	":created"=>$created
]);

if($rScores && $rPointsHistory && $rUsers){

	$response = [
		"score" => $score,
		"status" => 200
	];

	echo json_encode($response);
	die();
}

else{
	$e = $stmt->errorInfo();
	$response = ["status" => 400, "error" => $e];
	echo json_encode($response);
	die();
}
?>