<?php
include('g/phpgraphlib.php');
include 'themvars.php';
$connect=mysql_connect($server,$username,$password); 
		
mysql_select_db($database) or die( "Unable to open database");
if(strlen($_GET['comp'])==0)
{
	$query="Select * from Team T ORDER BY defense;";
}
else
{
	$query="Select * FROM `Team` T, isAt I WHERE T.teamno=I.teamno AND I.tname='".$_GET['comp']."' ORDER BY defense;";
}
$result=mysql_query($query,$connect);
if (!$result) 
{
	die('Invalid query: ' . mysql_error());
}

$data=array();
$data2=array();

while($row = mysql_fetch_array($result))
{
	//var_dump($row);
	//$point=$row['teamno']=>$row['reliability'];
	array_push($data,$row['teamno']);
	array_push($data2,$row['defense']);
}

$data=array_combine($data,$data2);

//var_dump($data);

$graph = new PHPGraphLib(942,700);
//$data = $data;//$_GET;//array("Jan"=>-1324, "Feb"=>-1200, "Mar"=>-100, "Apr"=>-1925, "May"=>-1444, "Jun"=>-957, "Jul"=>-364, "Aug"=>-221, "Sep"=>-1300, "Oct"=>-848, "Nov"=>-719, "Dec"=>-114);
$graph->addData($data);
$graph->setGradient("242,148,34","242,131,34");
$graph->setDataValues(false);
$graph->setTitle('Team Defense');
$graph->setTextColor("38,27,38");
$graph->setBackgroundColor("240,242,242");
$graph->createGraph();
?>