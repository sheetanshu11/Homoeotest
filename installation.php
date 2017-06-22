<?php
if ($_POST['install'] || file_exists('../environment.php')){
include("../headers.php");
if(file_exists('../environment.php')){
    require('../environment.php');
    echo "Environment exists. Using the same<br/>";
}
else{
    $dbhost = $_POST['dbhost'];
    $dbuser = $_POST['dbuser'];
    $dbpassword = $_POST['dbpassword'];
    $db = $_POST['db'];
    echo "Setting up environment<br/>";
    $fp = fopen("../environment.php","w") or die("Error creating environment file<br/>");
    fwrite($fp,'<?php
    $dbhost = "'.$dbhost.'";
    $dbuser = "'.$dbuser.'";
    $dbpassword = "'.$dbpassword.'";
    $db = "'.$db.'";
    ?>') or die(ferror($fp));
    fclose($fp);

}
echo $dbuser;
$c = new mysqli($dbhost,$dbuser,$dbpassword);
$query = "CREATE DATABASE IF NOT EXISTS $db";
$c->query($query);
echo $c->error;
$c->select_db($db);
$c->query("CREATE TABLE IF NOT EXISTS Active_Paper(
ID INT PRIMARY KEY AUTO_INCREMENT,
Pname VARCHAR(20),
Date date,
Time time
);");
$c->query("INSERT INTO Active_Paper(ID,Pname) VALUES(0,'Dummy')");
echo $c->error;
$c->query("CREATE TABLE Dummy(
ID INT PRIMARY KEY AUTO_INCREMENT,
Question VARCHAR(150),
Correct_Option VARCHAR(50),
Wrong_Options VARCHAR(150)
);");
echo $c->error;
$c->query("INSERT INTO Dummy VALUES(1,'Dummy question ?','Dummy correct answer','Dummy wrong answer');");
echo $c->error;
if($c->error)
	echo $c->error."<br/>";
else
	echo "Database Installed<br/><br/><small>If showing errors then install again</small><br/>";
flush();

echo "<br/>Setting up admin panel<br/>";
$fp = fopen(".htaccess","w") or die("Error setting admin/.htaccess<br/>");
fwrite($fp,'AuthType Basic
AuthName "Restricted area"
AuthUserFile '.getcwd().'/.htpasswd
Require valid-user');
fclose($fp);
echo "Installation completed";
}
else
{
    ?>
    <form method="POST">
    DB: <input type='text' name='db'/><br/>
    DB Host: <input type='text' name='dbhost'/><br/>
    DB User: <input type='text' name='dbuser'/><br/>
    DB Passord: <input type='text' name='dbpassword'/><br/>
    <input type='submit' name='install' value='Setup'/>
    </form>
    <?php
}
?>
				