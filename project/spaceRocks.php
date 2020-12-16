<?php require_once(__DIR__ . "/partials/nav.php"); ?>
<?php
/*
if (!is_logged_in()) {
    //this will redirect to login and kill the rest of this script (prevent it from executing)
    flash("You must be logged in to access this page");
    die(header("Location: login.php"));
}
*/
?>

<script>

var canvas;
var context;

var loop;

var player;
var acceleration = 0.2;
var topSpeed = 5;

var maxRockSize = 75;
var minRockSize = 50;
var rockThickness = 5;
var rockStages = 2;
var rockSpeed = 1;

var bulletSize = 5;
var bulletSpeed = 7.5;

var rocks = [];
var bullets = [];

var hits = 0;

var W = 87;
var S = 83;
var A = 65;
var D = 68;

var UP = 38;
var DOWN = 40;
var LEFT = 37;
var RIGHT = 39;

var keys = {
	W: false,
	S: false,
	A: false,
	D: false,
	UP: false,
	DOWN: false,
	LEFT: false,
	RIGHT: false
};

var leftScore = 0;
var rightScore = 0;

var bulletTimer = 0;
var bulletTimerMax = 15;

function init() {

	canvas = document.getElementById("board");
	if (canvas.getContext) {
		context = canvas.getContext("2d");
		
		//Make the player on the screen
		player = makeRect(canvas.width / 2 - 25, canvas.height / 2 - 25 / 2, 25, 25, '#FFFFFF');
		
		//Spawn rocks in the game
		for (i = 0; i < 4; i++) {
		  spawnRock();
		}
		
		//Put ball in the middle, start first match
		attachKeyListeners();
		
		loop = window.setInterval(gameLoop, 16); //16 ms interval
		canvas.focus();
		
	}
}

function spawnRock() {
	
	let coords = [0, 0];
	
	let rockVelocity = [rockSpeed, rockSpeed];
	
	//Randomly picks which side of the canvas a rock will be spawned.
	//0: x = 0, 1: x = canvas.width, 2: y = 0, 3: y = canvas.height;
	let canvasSide = Math.floor(Math.random() * 4);
	
	switch(canvasSide) {
	
		case 0:
		coords = [0 - maxRockSize, Math.random() * canvas.height];
		rockVelocity = [rockSpeed, (Math.random() * rockSpeed * 2) - rockSpeed];
		break;
		
		case 1:
		coords = [canvas.width + maxRockSize, Math.random() * canvas.height];
		rockVelocity = [rockSpeed * -1, (Math.random() * rockSpeed * 2) - rockSpeed];
		break;
		
		case 2:
		coords = [Math.random() * canvas.width, 0 - maxRockSize];
		rockVelocity = [(Math.random() * rockSpeed * 2) - rockSpeed, rockSpeed];
		break;
		
		case 3:
		coords = [Math.random() * canvas.width, canvas.height + maxRockSize];
		rockVelocity = [(Math.random() * rockSpeed * 2) - rockSpeed, rockSpeed * -1];
		break;
	}
	
	let rockSize = (Math.random() * (maxRockSize - minRockSize)) + minRockSize
	
	let newRock = makeOutlinedRect(coords[0], coords[1], rockSize, rockSize, rockThickness, rockVelocity[0], rockVelocity[1]);
	newRock.stage = rockStages;
	
	console.log(newRock.stage);
	
	rocks.push(newRock);
	
}

function splitRock(rock) {
	
	if (rock.stage > 0) {
		
		for (let i = 0; i < 2; i++) {
			
			let newRock = makeOutlinedRect(
				rock.x, 
				rock.y, 
				rock.w/1.25, 
				rock.h/1.25, 
				rockThickness, 
				((Math.random() * rockSpeed * 2) - rockSpeed) * 2, 
				((Math.random() * rockSpeed * 2) - rockSpeed) * 2
			);
			
			newRock.stage = rock.stage - 1;
			
			rocks.push(newRock);
		}
	}
	
	else { spawnRock(); }

}

function attachKeyListeners() {
	
	//is key being pressed?
	window.addEventListener('keydown', function(e) {
	
		//console.log("keydown", e);
		
		if (e.keyCode === W) { keys.W = true; }
		
		if (e.keyCode === S) { keys.S = true; }
		
		if (e.keyCode === A) { keys.A = true; }
		
		if (e.keyCode === D) { keys.D = true; }
		
		if (e.keyCode === UP) { keys.UP = true; }
		
		if (e.keyCode === DOWN) { keys.DOWN = true; }
		
        if (e.keyCode === LEFT) { keys.LEFT = true; }
		
        if (e.keyCode === RIGHT) { keys.RIGHT = true; }
		
		//console.log(keys);
	
	} );
	
	//is key not being pressed?
	window.addEventListener('keyup', function (e) {
        //console.log("keyup", e);
		
        if (e.keyCode === W) { keys.W = false; }
		
        if (e.keyCode === S) { keys.S = false; }
		
		if (e.keyCode === A) { keys.A = false; }
		
        if (e.keyCode === D) { keys.D = false; }
		
        if (e.keyCode === UP) { keys.UP = false; }
		
        if (e.keyCode === DOWN) { keys.DOWN = false; }
		
        if (e.keyCode === LEFT) { keys.LEFT = false; }
		
        if (e.keyCode === RIGHT) { keys.RIGHT = false; }
		
        //console.log(keys);
    });
	
}

//Create rectangle object
function makeRect(x, y, width, height, color) {

	if (!color) color = '#000000';
	
	//Associative array with a function that draws a rectangle based on the stored information
	return {
		x: x,
		y: y,
		sX: 0,
		sY: 0,
		w: width,
		h: height,
		c: color,
		draw: function () {
			context.fillStyle = this.c;
			context.fillRect(this.x, this.y, this.w, this.h);
		}
	};
}

function makeOutlinedRect(x, y, width, height, thickness, sX, sY, outlineColor, fillColor) {

	if (!outlineColor) outlineColor = '#FFFFFF';
	if (!fillColor) fillColor = '#000000';
	
	//Associative array with a function that draws a rectangle based on the stored information
	return {
		x: x,
		y: y,
		sX: sX,
		sY: sY,
		w: width,
		h: height,
		t: thickness,
		cO: outlineColor,
		cF: fillColor,
		draw: function () {
			context.fillStyle = this.cO;
			context.fillRect(this.x, this.y, this.w, this.h);
			
			context.fillStyle = this.cF;
			context.fillRect(this.x + this.t, this.y + this.t, this.w - this.t*2, this.h - this.t*2)
			
		},
		move: function () {
			this.x += this.sX;
			this.y += this.sY;
		}
	};
}

function movePaddle() {
	if (keys.W) { player.sY = Math.max(player.sY - 0.2, topSpeed * -1); }

	if (keys.S) { player.sY = Math.min(player.sY + 0.2, topSpeed); }
	
	if (keys.A) { player.sX = Math.max(player.sX - 0.2, topSpeed * -1); }
	
	if (keys.D) { player.sX = Math.min(player.sX + 0.2, topSpeed); }

	lapOverCanvas(player);
}

function checkShootBullet() {

	let x = 0;
	let y = 0;
	
	if (keys.UP) { y = -1; }

	else if (keys.DOWN) { y = 1; }
	
	else if (keys.LEFT) { x = -1; }
	
	else if (keys.RIGHT) { x = 1; }
	
	if ((x != 0 || y != 0) && bulletTimer == 0) {
		
		let newBullet = makeRect(player.x + (player.w/2), player.y + (player.h/2), bulletSize, bulletSize, bulletSpeed, '#FFFFFF');
		
		newBullet.sX = x * bulletSpeed;
		newBullet.sY = y * bulletSpeed;
		
		bullets.push(newBullet);
		
		bulletTimer = bulletTimerMax;
		
	}
}

function moveBullet(bullet, bulletIndex) {

	bullet.x += bullet.sX;
	bullet.y += bullet.sY;

}

function moveObj(obj) {
	
	player.x += player.sX;
	player.y += player.sY;
	
}

function lapOverCanvas(object) {
    if (object.y + object.h < 0) {
        object.y = canvas.height;
    }
    if (object.y > canvas.height) {
        object.y = 0 - object.h;
    }
	
	if (object.x + object.w < 0) {
        object.x = canvas.width;
    }
    if (object.x > canvas.width) {
        object.x = 0 - object.w;
    }
}

function checkHit(bullet, target) {

	let collided = true;

	//Is ball or right paddle on left side of the other?
    if ((bullet.x >= (target.x + target.w)) || (target.x >= (bullet.x + bullet.w))) {
        collided = false;
    }
	
	//Is ball or right paddle below the other?
	if (((bullet.y + bullet.h) <= target.y) || ((target.y + target.h) <= bullet.y)) {
        collided = false;
    }
	
	if (collided) { hits++; }
	
	return collided;

}

function drawScore() {
	// Draw the scores
	context.fillStyle = '#FFFFFF';
	context.font = '24px Arial';
	context.textAlign = 'center';
	context.fillText('Hits: ' + hits, canvas.width/2, canvas.height - 24);
	
}

function leftScreen(object) {

	if (object.y + object.h < 0 || object.y > canvas.height) {
		return true;
	}
	
	if (object.x + object.w < 0 || object.x > canvas.width) {
		return true;
	}
	
	return false;
}

function checkCollision(obj0, obj1) {
    
	let collided = true;
	
	//rectangle y + h = bottom side of the rectangle
	
	//Is ball or right paddle on left side of the other?
    if ((obj0.x >= (obj1.x + obj1.w)) || (obj1.x >= (obj0.x + obj0.w))) {
        collided = false;
    }
	
	//Is ball or right paddle below the other?
	if (((obj0.y + obj0.h) <= obj1.y) || ((obj1.y + obj1.h) <= obj0.y)) {
        collided = false;
    }
	
	return collided;
}

function erase() {
	context.fillStyle = '#000000';
	context.fillRect(0, 0, canvas.width, canvas.height);
}

function sendScore() {
	
	let xhttp = new XMLHttpRequest();
	
	xhttp.onreadystatechange = function () {
		if (this.readyState == 4 && this.status == 200) {
			
			console.log(this.responseText);
			
			let json = JSON.parse(this.responseText);
			if (json) {
				if (json.status == 200) { alert("YOU DIED!\nFINAL SCORE: " + json.score); }
				
				else if (json.status == 403) {alert("YOU DIED!\nFINAL SCORE: " + hits + "\nPlease log in or register to save your scores!");}
				
				else { alert("JSON.ERROR: " + json.error); }
			}
		}
	};
	
	xhttp.open("POST", "<?php echo getURL("api/newScore.php");?>", true);
	//this is required for post ajax calls to submit it as a form
	xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	//map any key/value data similar to query params
	xhttp.send("score=" + hits + "&gameName=Space Rocks");
	
	location.reload();
	
}

function gameLoop() {
	erase();
	movePaddle();
	
	//draw stuff
	player.draw();
	
	checkShootBullet();
	
	if (bulletTimer > 0) { bulletTimer--; }
	
	for (let i = 0; i < bullets.length; i++) {
		moveBullet(bullets[i], i);
		bullets[i].draw();
		
		if (leftScreen(bullets[i])) {
			//destroy bullet
			bullets.splice(i, 1);
		}
	}
	
	for (let i = 0; i < rocks.length; i++) {
		rocks[i].move();
		rocks[i].draw();
		
		lapOverCanvas(rocks[i]);
		
		if (checkCollision(rocks[i], player)) {
		
			clearInterval(loop);
			sendScore();
			
		}
		
		for (let j = 0; j < bullets.length; j++) {
			
			if (checkHit(bullets[j], rocks[i])) {
				//destroy bullet
				bullets.splice(j, 1);
				
				splitRock(rocks[i]);
				
				hitRock = rocks.splice(i, 1);
				
			}
			
		}
	} 
	
	moveObj(player);
	
	drawScore();
}
</script>
<html>
<body onload = "init();">
	<main>
		<canvas id="board" width="600px" height="600px" style="border: 1px solid black;"> </canvas>
	</main>
</body>
</html>
	