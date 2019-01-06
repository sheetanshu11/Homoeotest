<?php
try{
	file_exists("environment.php") or die("No environment present. Run admin/installation.php");
	require('environment.php');
	$c = new mysqli($dbhost,$dbuser,$dbpassword);
	$c->select_db($db);
}
	catch (Exception $e){
		echo "Fatal: Database not available. Create it by running admin/installation.php";
}
?>	
