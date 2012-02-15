<?php
include 'themvars.php';
require_once('stats.php');

$sql="SELECT T.teamno FROM Team T;";
$result=queryThis($sql);

while($row = mysql_fetch_array($result))
{
	//var_dump($row);
	//echo"<br/>";
	updateStats($row['teamno']);
}

header( 'Location: graphViewer.php' ) ;
?>