<?php
	session_start();
	$stratid = $_GET['stratid'];
	$stratinfo;	

	$con = new mysqli("localhost","stratshare","stratz","stratshare");
	$query = "select * from strats where id={$stratid}";
	$result = $con->query($query);
	
	if ($result->num_rows == 0){
		echo "This strat id does not exist!";
		exit;
	}else{
		$stratinfo = $result->fetch_assoc(); 	
	}
?>
<html>
<head>
<link rel="stylesheet" href="style.css" type="text/css">
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
<script>
$(document).ready(function(){
	var canvas = $('#viewer')[0].getContext("2d");
        var players = <?php echo $stratinfo['data']; ?>;
	var playercolors = ["green","blue","red","yellow","orange"];
	var ticks = 0;
	
	var map = new Image();
	map.src = '/maps/<?php echo $stratinfo['mapfile']; ?>';
	var flash = new Image();
	flash.src = '/images/flash.png';
	var frag = new Image();
	frag.src = '/images/frag.png';
	var smoke = new Image();
	smoke.src = '/images/smoke.png';

	setInterval(draw, 10);
	
	function draw(){
		canvas.clearRect(0, 0, 900, 900);
		canvas.drawImage(map, 0, 0, 900, 900);
		var progress = [0,0,0,0,0];
		
		$.each(players, function(player_index, player){
			canvas.beginPath();
			canvas.lineWidth = 5;
			canvas.lineCap = "round";
			canvas.strokeStyle = playercolors[player_index];
			var lastx;
			var lasty;
			$.each(player, function(action_index, action){
				if (action_index == 0){
					canvas.moveTo(action[1], action[2]);
					lastx = action[1];
					lasty = action[2];
				}else{
					switch(action[0]){
					case "run":
						var distance = Math.sqrt((Math.pow((action[1] - lastx),2)) + (Math.pow((action[2] -lasty),2)));
						if (ticks >= distance + progress[player_index]){
							canvas.lineTo(action[1], action[2]);
							progress[player_index] += distance;
							lastx =  action[1];
							lasty =  action[2];
						}else{
							canvas.lineTo((action[1] - lastx) * ((ticks - progress[player_index])/distance) + lastx, (action[2] -lasty) * ((ticks - progress[player_index])/distance) +lasty );
							return false;
						}
						break;
					case "walk":
						var distance = Math.sqrt((Math.pow((action[1] - lastx),2)) + (Math.pow((action[2] -lasty),2))) * 2;
						if (ticks >= distance + progress[player_index]){
							canvas.lineTo(action[1], action[2]);
							progress[player_index] += distance;
							lastx =  action[1];
							lasty =  action[2];
						}else{
							canvas.lineTo((action[1] - lastx) * ((ticks - progress[player_index])/distance) + lastx, (action[2] -lasty) * ((ticks - progress[player_index])/distance) +lasty );
							return false;
						}
						break;
					case "throwflash":
						var distance = 25;
						if (ticks >= distance + progress[player_index]){
							canvas.drawImage(flash, action[1], action[2], 50, 50);
							progress[player_index] += distance;
						}else{
							canvas.drawImage(flash, (action[1] - lastx) * ((ticks - progress[player_index])/distance) + lastx, (action[2] - lasty) * ((ticks - progress[player_index])/distance) + lasty, 50, 50);
							return false;
						}
						break;
					case "throwgrenade":
						var distance = 25;
						if (ticks >= distance + progress[player_index]){
							canvas.drawImage(frag, action[1], action[2], 50, 50);
							progress[player_index] += distance;
						}else{
							canvas.drawImage(frag, (action[1] - lastx) * ((ticks - progress[player_index])/distance) + lastx, (action[2] -lasty) * ((ticks - progress[player_index])/distance) +lasty, 50, 50);
							return false;
						}
						break;
					case "throwsmoke":
						var distance = 25;
						if (ticks >= distance + progress[player_index]){
							canvas.drawImage(smoke, action[1], action[2], 50, 50);
							progress[player_index] += distance;
						}else{
							canvas.drawImage(smoke, (action[1] - lastx) * ((ticks - progress[player_index])/distance) + lastx, (action[2] -lasty) * ((ticks - progress[player_index])/distance) +lasty, 50, 50);
							return false;
						}
						break;
					}
				}
			});
			canvas.stroke();	
		});
		
		ticks++;
	}
});
</script>
</head>
<body>
<canvas id="viewer" width=900 height=900>
</canvas>
</body>
</html>
