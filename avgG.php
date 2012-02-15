<?php
include('g/phpgraphlib.php');
include 'themvars.php';
$connect=mysql_connect($server,$username,$password); 
		
mysql_select_db($database) or die( "Unable to open database");
if(strlen($_GET['comp'])==0)
{
	$query = "SELECT S.sid, S.raw, S.penalty, S.final FROM Score S, Team T WHERE S.sid=T.teamno ORDER BY final";
}
else
{
	$query = "SELECT S.sid, S.raw, S.penalty, S.final FROM Score S, isAt I WHERE S.sid=I.teamno AND I.tname='".$_GET['comp']."' ORDER BY final;";
}
$result=mysql_query($query,$connect);
if (!$result) 
{
	die('Invalid query: ' . mysql_error());
}

$data=array();
$data2=array();
$data3=array();
$data4=array();

while($row = mysql_fetch_array($result))
{
	array_push($data,$row['sid']);
	array_push($data2,$row['final']);
	array_push($data3,$row['penalty']);
	array_push($data4,$row['raw']);
}

$final=array_combine($data,$data2);
$penalty=array_combine($data,$data3);
$raw=array_combine($data,$data4);

$graph = new PHPGraphLib(942,700);
$graph->addData($final,$penalty,$raw);
$graph->setGradient("242,148,34","242,131,34");
$graph->setDataValues(false);
$graph->setTitle('Team Score');
$graph->setTextColor("38,27,38");
$graph->setBackgroundColor("240,242,242");
$graph->setLegend(true);
$graph->setLegendTitle('Final','Penalty','Raw');
$graph->createGraph();
?>