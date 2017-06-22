<?php
include("headers.php");
require("connect.php");
$res = $c->query("SELECT Pname FROM Active_Paper ORDER BY ID DESC LIMIT 1");
$current_p = $res->fetch_row();
$current_p = $current_p[0];
$res = $c->query("SELECT * FROM $current_p");
$json = "[";
while($row = $res->fetch_assoc()){
		if($json != "[") $json .= ",";
		$json .= "{'question':'{$row['Question']}','correct_option':'{$row['Correct_Option']}','all_options':'{$row['Correct_Option']};{$row['Wrong_Options']}'}";
}
$json .= "]";
?>
<script src="jquery-2.1.4.min.js"></script>
<link rel='stylesheet' type='text/css' href='css/external.css'/>
<style type='text/css'>
#score th{
text-align: left;
}
body{
background-image: url(images/bg.jpg);
max-height:100%;
}
html{
height:100%;
}
</style>
<script>
q = <?=$json?>;
i = 0;
answers = {};
function reset(){
i = 0;
answers = {};
$("#results").html("");
next_question();
}
function next_question(){
options = q[i].all_options.split(";");
for(var index=0;index<options.length;index++){ //Shuffle
	rindex = Math.floor((Math.random() * 1000) % options.length);
	tmp = options[rindex];
	options[rindex] = options[index];
	options[index] = tmp;
}
sheet ="<table width='100%' height='100%'><tr><th colspan=2 style=' font-family: \"Nunito\", sans-serif;font-weight: bold;color:pink;font-size:30px'>";
sheet += q[i].question;
sheet += "</th></tr><tr>";
for(index=0; index<options.length;index++){
//alert((100/(options.length/2))+"%");
	sheet += "<td width='50%' height="+(100/(options.length/2))+"%' style='font-size:2em'><a class='button' onclick='register("+i+","+index+")';><span>"+options[index]+"</span>	</a></td>";
	if(index % 2){
		sheet += "<br/></tr><tr>";
	}
}
sheet +="</tr></table>";
$("#q").hide();
$("#q").html(sheet);
$("#q").fadeIn()
i++;
}
function register(i,index){
try{
answers[i] = options[index];
next_question();
}
catch (e){ //Questions finished
i++;
results="<table border=1 id='score'>";
results+=" <legend>Results</legend>";
results+="<tr><th>Question</th><th>Your Option</th><th>Correct Option</th><th>Result</th></tr>";
	for(j=0,correct=0;j<i;j++){ //Iterating over all questions
		results+="<tr><td>"+q[j].question+"</td><td>"+answers[j]+"</td><td>"+q[j].correct_option+"</td>";
		if(answers[j] == q[j].correct_option){
			results+="<td><img src='images/correct2.jpg' alt='Correct' height='20' width='20'/></td>";
			correct++;
			}
		else
			results+="<td><img src='images/incorrect.png' alt='Incorrect' height='20' width='20'/></td>";
		results+="</tr>\n";
}
results+="</table><br/>";

scoredata="<table id='score'><tr><th >Correct:</th><td>"+correct+"</td></tr><tr><th <th >Incorrect:</th><td>"+(i-correct)+"</td></tr><tr><th <th >Total Questions:</th><td>"+i+"</td></tr><tr><th <th >Percentage:</th><td> "+correct/i * 100+"%</td></tr></table>";
$("#q").animate({height:'toggle'});
$("#q").fadeOut();
$("#results").show();
$("#results").html(results);
$("#results").append(scoredata);
$("#results").append("<a onclick='reset()' class='button-0'>Retry</a>");
}
}
</script>
<body onload='next_question()'>
<div id='q' style='display:block;height:60%;'>
</div>
<div id='results' style='display:none'>
</div>
</body>