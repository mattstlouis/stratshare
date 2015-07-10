<?php
	$con = new mysqli("localhost","stratshare","stratz","stratshare");
	$savestate = $_POST['savedata'];
	$savestate = addslashes($savestate);
	$map = $_POST['map'];
	$map = addslashes($map);
	$con->query("insert into strats (data, mapfile) VALUES ('$savestate','$map')");	
	echo $con->insert_id;
?>
