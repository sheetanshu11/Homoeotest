<?php
include("../headers.php");
require("../connect.php");
date_default_timezone_set("Asia/Calcutta");	
function fatal_check($c,$pname) { //if fatal - echo exit
	if($c->error){
		echo "Error: ".$c->error;
		die();
	}
}
?>
<?php
if($_SERVER['REQUEST_METHOD']!='POST') { 
?>
<script>
function gen_q_fields(){
	html ="";
	no = document.getElementById('no_of_q').value;
	for(i=0;i<no;i++)
		html += "Question: <input type='text' name='question["+i+"]'/> ?<br/>Correct Option: <input type='text' name='correct_option["+i+"]'/><br/>Wrong Options: <input type='text' name='wrong_options["+i+"]' placeholder='Seperate by ; '/><hr/>"
	que=document.getElementById('que');
	que.innerHTML="<fieldset><legend>"+document.getElementById('pname').value+"</legend>"+html+"</fieldset><input type='hidden' name='total_q' value="+i+"/>";
}
</script>	
<form method='post'>
Paper name: <input type='text' name='pname' id='pname' placeholder='Do NOT use special chars'/><br/>
No of questions: <input type='text' id='no_of_q' size=3 maxlength=3 onkeyup='gen_q_fields();'/><br/>
<span id='que'> 
</span>
<input type='submit' value='Add Questions' name='create_p' />
</form>

<?php
$res = $c->query("SELECT Pname FROM Active_Paper ORDER BY ID DESC LIMIT 1");
if(!($current_p = $res->fetch_row()))
	echo "<span style='color:red'>Warning: No active paper, select one</span>";
?>
<form method='post'>
Active paper: <select name='pname'>
<?php
$res = $c->query("show tables");
while($row = $res->fetch_row())
	if($row[0]==$current_p[0])
		echo "<option name='$row[0]' selected>$row[0]</option>\n";
	else if($row[0] != 'active_paper' && $row[0] != 'Active_Paper')
		echo "<option name='$row[0]'>$row[0]</option>\n";
echo "</select>\n<input type='submit' value='Change' name='change_p'>\n<input type='submit' name='del_p' value='Delete Paper'/>\n</form>";
}
else if(isset($_POST['create_p'])){
$pname = preg_replace("#[\\\\'\"\s=\-!/]#","_",$c->real_escape_string($_POST['pname']));
$total_q = $_POST['total_q'];
$c->query("CREATE TABLE $pname(
ID INT PRIMARY KEY AUTO_INCREMENT,
Question VARCHAR(150),
Correct_Option VARCHAR(50),
Wrong_Options VARCHAR(150)
)");
fatal_check($c,$pname);
$stmt = $c->prepare("INSERT INTO $pname(Question,Correct_Option,Wrong_Options	) VALUES(?,?,?)");
fatal_check($c,$pname);
$stmt -> bind_param("sss",$question,$correct_option,$wrong_options);
for($i=0;$i<$total_q;$i++){
	$question = $_POST["question"][$i]." ?";
	$correct_option = $_POST["correct_option"][$i];
	$wrong_options = $_POST["wrong_options"][$i];
	$stmt -> execute();
}
echo "Paper <i>$pname</i> added";
echo "<form><input type='submit' value='Continue'/></form>";
}

else if(isset($_POST['change_p'])){
$pname = $c->real_escape_string($_POST['pname']); 
$c->query("INSERT INTO Active_Paper(Pname,Date,Time) values('$pname','".date("Y:m:d")."','".date('H:i:s')."')");
echo $c->error; //if any
echo "Paper <i>$pname</i> published successfully";
echo "<form><input type='submit' value='Continue'/></form>";
}
else if(isset($_POST['del_p'])){
$p = $c->real_escape_string($_POST['pname']);
$c->query("DROP TABLE $p");
if($c->error)
	echo "Error ".$c->error;
else
	echo "Paper <i>$p</i> deleted successfully.".
        "<form><input type='submit' value='Continue'/></form>";
}
$c->close();
?>