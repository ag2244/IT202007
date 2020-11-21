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
var rightPaddle;
var paddleWidth = 25;
var paddleHeight = 100;
var paddleSpeed = 2;

var bulletSize = 5;
var bulletSpeed = 5;

var drawables = [];

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
		
		//Make the paddles on the screen
		player = makeRect(25, canvas.height / 2 - paddleHeight / 2, paddleWidth, paddleHeight, 5, '#BC0000');
		rightPaddle = makeRect(canvas.width - paddleWidth - 25, canvas.height / 2 - paddleHeight / 2, paddleWidth, paddleHeight, 5, '#0000BC');
		
		//put drawable objects into drawable array
		drawables.push(player);
		drawables.push(rightPaddle);
		
		console.log(drawables);		
		
		//Put ball in the middle, start first match
		attachKeyListeners();
		
		loop = window.setInterval(gameLoop, 16); //16 ms interval
		canvas.focus();
		
	}
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
function makeRect(x, y, width, height, speed, color) {

	if (!color) color = '#000000';
	
	//Associative array with a function that draws a rectangle based on the stored information
	return {
		x: x,
		y: y,
		w: width,
		h: height,
		s: speed,
		c: color,
		draw: function () {
			context.fillStyle = this.c;
			context.fillRect(this.x, this.y, this.w, this.h);
		}
	};
}

function movePaddle() {
	if (keys.W) { player.y -= player.s; }

	if (keys.S) { player.y += player.s; }
	
	if (keys.A) { player.x -= player.s; }
	
	if (keys.D) { player.x += player.s; }

	clampToCanvas(player);
}

function checkShootBullet() {

	let x = 0;
	let y = 0;
	
	if (keys.UP) { y = -1; }

	else if (keys.DOWN) { y = 1; }
	
	else if (keys.LEFT) { x = -1; }
	
	else if (keys.RIGHT) { x = 1; }
	
	if ((x != 0 || y != 0) && bulletTimer == 0) {
		
		let newBullet = makeRect(player.x + (player.w/2), player.y + (player.h/2), bulletSize, bulletSize, bulletSpeed, '#000000');
		
		newBullet.sX = x * bulletSpeed;
		newBullet.sY = y * bulletSpeed;
		
		bullets.push(newBullet);
		
		bulletTimer = bulletTimerMax;
		
	}
}

function moveBullet(bullet, bulletIndex) {

	bullet.x += bullet.sX;
	bullet.y += bullet.sY;

	// Bounce the ball off the top/bottom
	if (bullet.y + bullet.h < 0 || bullet.y > canvas.height) {
		bullets.splice(bulletIndex, 1);
	}
	
	if (bullet.x + bullet.w < 0 || bullet.x > canvas.width) {
		bullets.splice(bulletIndex, 1);
	}

}

//Make sure a paddle doesnt exit the canvas
function clampToCanvas(paddle) {
    if (paddle.y < 0) {
        paddle.y = 0;
    }
    if (paddle.y + paddle.h > canvas.height) {
        paddle.y = canvas.height - paddle.h;
    }
	
	if (paddle.x < 0) {
        paddle.x = 0;
    }
    if (paddle.x + paddle.w > canvas.width) {
        paddle.x = canvas.width - paddle.w;
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
	context.fillStyle = '#000000';
	context.font = '24px Arial';
	context.textAlign = 'center';
	context.fillText('Hits: ' + hits, canvas.width/2, canvas.height - 24);
	
}

function checkCollision() {
    
	let collided = true;
	
	//rectangle y + h = bottom side of the rectangle
	
	//Is ball or right paddle on left side of the other?
    if ((player.x >= (rightPaddle.x + rightPaddle.w)) || (rightPaddle.x >= (player.x + player.w))) {
        collided = false;
    }
	
	//Is ball or right paddle below the other?
	if (((player.y + player.h) <= rightPaddle.y) || ((rightPaddle.y + rightPaddle.h) <= player.y)) {
        collided = false;
    }
	
	/*if (collided) {
	
		context.fillStyle = '#000000';
		context.font = '24px Arial';
		context.textAlign = 'center';
		context.fillText('Two paddles have collided!', canvas.width/2, 24);
	
	}*/
	
	return collided;
}

function erase() {
	context.fillStyle = '#FFFFFF';
	context.fillRect(0, 0, canvas.width, canvas.height);
}

function sendScore() {

		
	
}

function checkDied() {

	if (checkCollision()) {
	
		clearInterval(loop);
		
		//erase();
		
		context.fillStyle = '#000000';
		context.font = '24px Arial';
		context.textAlign = 'center';
		context.fillText('Dead: Not big surprise', canvas.width/2, 24);
		context.fillText('Final Score: ' + hits, canvas.width/2, 50);
		
		//sendScore();
		
	}

}

function gameLoop() {
	erase();
	movePaddle();
	
	//draw stuff
	for (let i = 0; i < drawables.length; i++) {
		drawables[i].draw();
	}
	
	checkShootBullet();
	
	if (bulletTimer > 0) { bulletTimer--; }
	
	for (let i = 0; i < bullets.length; i++) {
		moveBullet(bullets[i], i);
		
		if (checkHit(bullets[i], rightPaddle)) {
			//destroy bullet
			bullets.splice(i, 1);
		}
		
		else { bullets[i].draw(); }
	}
	
	drawScore()
	
	checkDied();
}

</script>
<html>
<body onload = "init();">
	<main>
		<canvas id="board" width="600px" height="600px" style="border: 1px solid black;"> </canvas>
	</main>
</body>
</html>
	
