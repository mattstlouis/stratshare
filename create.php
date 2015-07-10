<html>
<head>
<link rel="stylesheet" href="style.css" type="text/css">
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
<script src="json2.js"></script>
<script>
$(document).ready(function(){
	var canvas = $('#viewer')[0].getContext("2d");
        var players = [[],[],[],[],[]];
	var playercolors = ["green","blue","red","yellow","orange"];
	
	var map = new Image();
	map.src = '/maps/dedust.jpg';
	var flash = new Image();
	flash.src = '/images/flash.png';
	var frag = new Image();
	frag.src = '/images/frag.png';
	var smoke = new Image();
	smoke.src = '/images/smoke.png';
	
	setInterval(draw, 50);
	
	$("#viewer").click(function(e){
		var player = $("input:radio[name=player]:checked").val();
		players[player].push([$("input:radio[name=action]:checked").val(), e.offsetX, e.offsetY]);
		console.log(players);
	});

	function draw(){
		canvas.clearRect(0, 0, 900, 900);
		canvas.drawImage(map, 0, 0, 900, 900);
		
		$.each(players, function(player_index, player){
			canvas.beginPath();
			canvas.lineWidth = 5;
			canvas.lineCap = "round";
			canvas.strokeStyle = playercolors[player_index];
			$.each(player, function(action_index, action){
					if ((action[0] == "run") || (action[0] == "walk")){
						if (action_index == 0){
							canvas.moveTo(action[1], action[2]);	
						}else{
							canvas.lineTo(action[1], action[2]);
						}
					}else{
						switch(action[0]){
						case "throwflash":
							canvas.drawImage(flash, action[1], action[2], 50, 50);
							break;
						case "throwsmoke":
							canvas.drawImage(smoke, action[1], action[2], 50, 50);
							break;
						case "throwgrenade":
							canvas.drawImage(frag, action[1], action[2], 50, 50);
							break;
						}
					}
			});
			canvas.stroke();	
		});
	}

	/* All non-canvas related js */
	$("#save").click(function(){
		var stringedout = JSON.stringify(players);
		console.log(stringedout);
		dataString = "map=" + $("input:radio[name=map]:checked").val() + ".jpg" + "&savedata=" + stringedout;
		console.log(players);
		$.ajax({
			type: "POST",
			url: "savestrat.php",
			data: dataString,
			success: function(b){
				alert("Successfully Saved as strat " + b);	
			}
		});
	});
	
	$("input:radio[name=map]").change(function(){
			map.src = '/maps/' + $("input:radio[name=map]:checked").val() + '.jpg';
	});
});
</script>
</head>
<body>
<div id="main_container">
<div id="toolbox_left">
<h1>Tools</h1>
<h2>Players</h2>
<input type="radio" name="player" value="0" checked>Player 1<br />
<input type="radio" name="player" value="1">Player 2<br />
<input type="radio" name="player" value="2">Player 3<br />
<input type="radio" name="player" value="3">Player 4<br />
<input type="radio" name="player" value="4">Player 5<br />
<h2>Actions</h2>
<input type="radio" name="action" value="run" checked>Run<br />
<input type="radio" name="action" value="walk">Walk<br />
<input type="radio" name="action" value="throwflash">Throw Flashbang<br />
<input type="radio" name="action" value="throwgrenade">Throw Grenade<br />
<input type="radio" name="action" value="throwsmoke">Throw Smoke<br />
<input type="radio" name="action" value="throwmaltov">Throw Maltov<br />
<input type="radio" name="action" value="throwdecoy">Throw Decoy<br />
<h2>Maps</h2>
<input type="radio" name="map" value="dedust" checked>de_dust<br />
<input type="radio" name="map" value="dedust2">de_dust2<br />
<input type="radio" name="map" value="detrain">de_train<br />
<input type="radio" name="map" value="deinferno">de_inferno<br />
<br /><br />
<input id="save" type="submit" value="Save">
</div>
<canvas id="viewer" width=900 height=900>
</canvas>
</div>
</div>
</body>
</html>
