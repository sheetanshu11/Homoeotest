<?php
require('../environment.php');
$c = new mysqli($dbhost,$dbuser,$dbpassword);
try{
$c->select_db($db);
}
catch (Exception $e){
echo "Fatal: Database not available. Create it by running installation.php";
}
?>	
