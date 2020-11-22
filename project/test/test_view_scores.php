<?php require_once(__DIR__ . "/../partials/nav.php"); ?>

<?php
if (!has_role("Admin")) {
    //this will redirect to login and kill the rest of this script (prevent it from executing)
    flash("You don't have permission to access this page");
    die(header("Location: /../login.php"));
}
?>
<?php
//we'll put this at the top so both php block have access to it
if (isset($_GET["id"])) {
    $id = $_GET["id"];
}
?>
<?php
//fetching
$result = [];
if (isset($id)) {
    $db = getDB();
    $stmt = $db->prepare("SELECT Scores.id, user_id, score, Users.username FROM Scores JOIN Users ON Scores.user_id = Users.id WHERE Scores.id = :id");
    $r = $stmt->execute([":id" => $id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$result) {
        $e = $stmt->errorInfo();
        flash($e[2]);
    }
}
?>
<?php if (isset($result) && !empty($result)): ?>
    <div class="card">
        <div class="card-title"></div>
		
        <div class="card-body">
            <div>
                <p><b>Stats</b></p>
				
				<div><b>User:</b> <?php safer_echo($result["username"]); ?> </div>
				
                <div><b>Scores Entry ID:</b> <?php safer_echo($result["id"]); ?></div>
				
				<div><b>User ID:</b> <?php safer_echo($result["user_id"]); ?></div>
				
                <div><b>Score:</b> <?php safer_echo($result["score"]); ?></div>
				
                <!--<div><b>Time Created:</b> <?php //safer_echo($result["created"]); ?></div> -->
				
            </div>
        </div>
    </div>
<?php else: ?>
    <p>Error looking up id...</p>
<?php endif; ?>
<?php require(__DIR__ . "/../partials/flash.php");