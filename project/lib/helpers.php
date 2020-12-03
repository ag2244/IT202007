<?php
session_start();//we can start our session here so we don't need to worry about it on other pages
require_once(__DIR__ . "/db.php");

function getTopLifetime() {
	
	$userID = get_user_id();
	$db = getDB();
	
	$stmt = $db->prepare("SELECT created, score FROM Scores WHERE user_id = :user_id ORDER BY score DESC LIMIT 10");
	$stmt->execute([ ":user_id" => $userID ]);
	$topScores = $stmt->fetchAll(PDO::FETCH_ASSOC);
	
	if (!$topScores) {return "No scores available";}
	
	return $topScores;
}

function getTopWeekly() {
	
	$userID = get_user_id();
	$db = getDB();
	
	$lastWeek = date("Y-m-d H:i:s", strtotime("-7 days"));
	
	$stmt = $db->prepare("SELECT created, score FROM Scores WHERE user_id = :user_id AND created >= :lastWeek ORDER BY score DESC LIMIT 10");
	$stmt->execute([
		":user_id" => $userID,
		":lastWeek" => $lastWeek
		]);
	$topScores = $stmt->fetchAll(PDO::FETCH_ASSOC);
	
	if (!$topScores) {return "No scores available";}
	
	return $topScores;
	
}

function getTopMonthly() {

	$userID = get_user_id();
	$db = getDB();
	
	$lastMonth = date("Y-m-d H:i:s", strtotime("-30 days"));
	
	$stmt = $db->prepare("SELECT created, score FROM Scores WHERE user_id = :user_id AND created >= :lastMonth ORDER BY score DESC LIMIT 10");
	$stmt->execute([
		":user_id" => $userID,
		":lastMonth" => $lastMonth
		]);
	$topScores["monthly"] = $stmt->fetchAll(PDO::FETCH_ASSOC);
	
	if (!$topScores) {return "No scores available";}
	
	return $topScores;
	
}

//Check if user is logged in
function is_logged_in(){
	return isset($_SESSION["user"]);
}

//Check if user has a role
function has_role($role){
	if(is_logged_in() && isset($_SESSION["user"]["roles"])){
		foreach($_SESSION["user"]["roles"] as $r){
			if($r["name"] == $role){
				return true;
			}
		}
	}
    return false;
}

function get_username() {
    if (is_logged_in() && isset($_SESSION["user"]["username"])) {
        return $_SESSION["user"]["username"];
    }
    return "";
}

function get_email() {
    if (is_logged_in() && isset($_SESSION["user"]["email"])) {
        return $_SESSION["user"]["email"];
    }
    return "";
}

function get_user_id() {
    if (is_logged_in() && isset($_SESSION["user"]["id"])) {
        return $_SESSION["user"]["id"];
    }
    return -1;
}

function getLifetimePoints() {
	if (is_logged_in() && isset($_SESSION["user"]["lifetimePoints"])) {
        return $_SESSION["user"]["lifetimePoints"];
    }
	return -1;
}

function safer_echo($var) {
    if (!isset($var)) {
        echo "";
        return;
    }
    echo htmlspecialchars($var, ENT_QUOTES, "UTF-8");
}

//for flash feature
function flash($msg) {
    if (isset($_SESSION['flash'])) {
        array_push($_SESSION['flash'], $msg);
    }
    else {
        $_SESSION['flash'] = array();
        array_push($_SESSION['flash'], $msg);
    }

}

function getMessages() {
    if (isset($_SESSION['flash'])) {
        $flashes = $_SESSION['flash'];
        $_SESSION['flash'] = array();
        return $flashes;
    }
    return array();
}

function getURL($path) {
    if (substr($path, 0, 1) == "/") {
        return $path;
    }
    return $_SERVER["CONTEXT_PREFIX"] . "/project/$path";
}
//end flash

?>






