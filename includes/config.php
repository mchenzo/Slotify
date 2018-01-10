<?php 

	ob_start();
	session_start();

	$timezone = date_default_timezone_set('America/Los_Angeles');

	$con = mysqli_connect('localhost', 'root', '', 'Slotify'); 	//server, default username 'root', empty password string, name of db

	if(mysqli_connect_errno()) {
		echo 'Failed to connect: ' . mysqli_connect_errno();
	}

?>