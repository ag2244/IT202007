<?php
session_start();//we can start our session here so we don't need to worry about it on other pages
require_once(__DIR__ . "/db.php");

function endCompetition($compID) {
	
	$db = getDB();
	
	$stmt = $db->prepare("SELECT * FROM Competitions WHERE id = :id");
	$stmt->execute([ ":id" => $compID ]);
	$compInfo = $stmt->fetch(PDO::FETCH_ASSOC);
	
	//If competition has not been paid out and is expired:
	if ((date('Y-m-d H:i:s') >= $compInfo["expires"]) && ((int)$compInfo["paid_out"] == 0)) {
	
		//If competition has more than three participants
		if ($compInfo["participants"] >= 3) {
			
			//Get all the scores
			$stmt = $db->prepare("SELECT * FROM Scores ORDER BY score DESC");
			$stmt->execute([ ":startDate"=>$compInfo["created"], ":endDate"=>$compInfo["expires"] ]);
			$allScores = $stmt->fetchAll(PDO::FETCH_ASSOC);
			
			$topThree = array();
			$numUniques = 0;
			
			foreach ($allScores as $score) {
				
				/*test for date comparisons
				var_dump($score["created"]);
				echo("<br>");
				var_dump($compInfo["created"]);
				echo("<br>");
				var_dump($compInfo["expires"]);
				echo("<br>");
				echo("<br>"); 
				*/
				
				//continue if score is not for this competition; i.e. does not fit within the time period.
				if (($score["created"] <= $compInfo["created"]) || ($score["created"] >= $compInfo["expires"])) {
					continue;
				}
				
				//If this user already has a saved score, resave the score as the max of these two
				if (array_key_exists($score["user_id"], $topThree)) {
						$topThree[$score["user_id"]]["score"] = max($score["score"], $topThree[$score["user_id"]]);
				}
				
				//Else, if the score is above the minimum score, set the user's top score as this score
				else if ($score["score"] >= $compInfo["min_score"]) { $topThree[$score["user_id"]]["score"] = $score["score"]; }
				
				//If we have found the top three scores with unique players, break the loop
				if (sizeof($topThree) >= 3) { break; }
			} 
			
			//Check if topThree has a size of 3 once more to make sure
			//Then pay them out
			if (sizeof($topThree) >= 3) {
				
				//List of keys (top three players)
				$topThreePlayers = array_keys( $topThree );
				
				$topThree[$topThreePlayers[0]]["share"] = (float)$compInfo["first_place_per"];
				$topThree[$topThreePlayers[0]]["placement"] = "First";
				
				$topThree[$topThreePlayers[1]]["share"] = (float)$compInfo["second_place_per"];
				$topThree[$topThreePlayers[1]]["placement"] = "Second";
				
				$topThree[$topThreePlayers[2]]["share"] = (float)$compInfo["third_place_per"];
				$topThree[$topThreePlayers[2]]["placement"] = "Third";
				
				foreach ($topThree as $user_id=>$userInfo) {
					
					//Get the share of the reward for this player
					$share = round($userInfo["share"] * $compInfo["reward"] / 100);
					
					var_dump($share);
					
					//Give the player his share in lifetimePoints
					$stmt = $db->prepare("UPDATE Users SET lifetimePoints = lifetimePoints + :reward WHERE id = :user_id");
					$r = $stmt->execute([ ":user_id"=>$user_id, ":reward"=>$share ]);
					
					//Update PointsHistory to reflect the change
					$stmt = $db->prepare("INSERT INTO PointsHistory (user_id, points_change, reason) VALUES (:user_id, :reward, :reason)");
					$r = $stmt->execute([ 
						":user_id"=>$user_id, 
						":reward"=>$share, 
						":reason"=>"Awarded ".$userInfo["placement"]." place in Competition with ID ".$compInfo["id"]
					]); 
					echo("<br>");
				}
				
			}
			
			//set paid_out to 0
			$stmt = $db->prepare("UPDATE Competitions SET paid_out = TRUE WHERE id = :id");
			$stmt->execute([ ":id" => $compID ]);
			
			return $topThree; 
			
		}
	
	}
	
	return "payoutFailed";
} 

function getProfileLink($userInfo) {
	
	if (isset($userInfo["id"]) && isset($userInfo["username"])) {
		
		echo join ('',
		["<a href='",
		getURL("profile.php"),
		"?id=",
		$userInfo["id"],
		"'>",
		$userInfo["username"],
		""]
		);
	}
	
	else { flash ("UNABLE TO GET LINK"); }
	
}

function userIsPublic($userID) {

	$db = getDB();
	
	$stmt = $db->prepare("SELECT isPublic FROM Users WHERE id = :user_id LIMIT 1");
	$stmt->execute([
		":user_id" => $userID
		]);
		
	return (int)$stmt->fetch(PDO::FETCH_ASSOC)["isPublic"];
	
}

function getTopLifetime($userID) {
	
	$db = getDB();
	
	$stmt = $db->prepare("SELECT created, score FROM Scores WHERE user_id = :user_id ORDER BY score DESC");
	$stmt->execute([ ":user_id" => $userID ]);
	$topScores = $stmt->fetchAll(PDO::FETCH_ASSOC);
	
	if (!$topScores) {return null;}
	
	return $topScores;
}

function getTopWeekly($userID) {
	
	$db = getDB();
	
	$lastWeek = date("Y-m-d H:i:s", strtotime("-7 days"));
	
	$stmt = $db->prepare("SELECT created, score FROM Scores WHERE user_id = :user_id AND created >= :lastWeek ORDER BY score DESC");
	$stmt->execute([
		":user_id" => $userID,
		":lastWeek" => $lastWeek
		]);
	$topScores = $stmt->fetchAll(PDO::FETCH_ASSOC);
	
	if (!$topScores) {return null;}
	
	return $topScores;
	
}

function getTopMonthly($userID) {

	$db = getDB();
	
	$lastMonth = date("Y-m-d H:i:s", strtotime("-30 days"));
	
	$stmt = $db->prepare("SELECT created, score FROM Scores WHERE user_id = :user_id AND created >= :lastMonth ORDER BY score DESC");
	$stmt->execute([
		":user_id" => $userID,
		":lastMonth" => $lastMonth
		]);
	$topScores = $stmt->fetchAll(PDO::FETCH_ASSOC);
	
	if (!$topScores) {return null;}
	
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

function getOtherUserInfo($userID) {

	$db = getDB();
	
	$stmt = $db->prepare("SELECT username, id, email, lifetimePoints FROM Users WHERE id = :user_id LIMIT 1");
	$stmt->execute([
		":user_id" => $userID
		]);
		
	$user = $stmt->fetch(PDO::FETCH_ASSOC);
	
	if (!$user) {return null;}
	
	return $user;
	
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

function test($param) {
	
	if (isset($param)) { return "YESPARAM"; }
	
	return "NOPARAM";
	
}

?>






