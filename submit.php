<?php
//error_reporting(E_ALL);
//ini_set('display_errors', 'On');
//echo "Hello world!<br/>";
include 'themvars.php';
//echo "themvars<br/>";
require_once('stats.php');//include 'stats.php';
//echo "stats<br/>";

$connect=mysql_connect($server,$username,$password); 
mysql_select_db($database) or die( "Unable to open database");

//echo "query function<br/>";

function MatchName($tourn,$session,$matchno)
{
	return substr($tourn,0,5).$session.$matchno;
}

function AllianceName($tourn,$session,$matchno,$color)
{
	return substr($tourn,0,5).$session.$matchno.$color;
}
//echo "string gen functions<br/>";

function insertTeam($teamno,$connect)
{
	return 'INSERT IGNORE INTO Team Values(\''.mysql_real_escape_string($teamno,$connect).'\',NULL,NULL,NULL,\''.mysql_real_escape_string($teamno,$connect).'\');';
}

function insertTeamAvgScore($teamno,$connect)
{
	return 'INSERT IGNORE INTO Score Values(\''.mysql_real_escape_string($teamno,$connect).'\',NULL,NULL,NULL);';
}

function insertIsAt($teamno,$tname,$connect)
{
	return 'INSERT IGNORE INTO isAt Values(\''.mysql_real_escape_string($teamno,$connect).'\',\''.mysql_real_escape_string($tname,$connect).'\');';
}

function insertAlliance($teamno1,$teamno2,$teamno3,$tourn,$session,$matchno,$color,$connect)
{
	return 'INSERT IGNORE INTO Alliance Values(\''.mysql_real_escape_string(AllianceName($tourn,$session,$matchno,$color),$connect).'\',\''.mysql_real_escape_string(AllianceName($tourn,$session,$matchno,$color),$connect).'\',\''.mysql_real_escape_string($teamno1,$connect).'\',\''.mysql_real_escape_string($teamno2,$connect).'\',\''.mysql_real_escape_string($teamno3,$connect).'\');';//dcfe700ace79a58e7b71975b3b97d58e
}

function insertScore($raw,$penalty,$final,$tourn,$session,$matchno,$color,$connect)
{
	return 'INSERT IGNORE INTO Score Values(\''.mysql_real_escape_string(AllianceName($tourn,$session,$matchno,$color),$connect).'\','.mysql_real_escape_string($raw,$connect).','.mysql_real_escape_string($penalty,$connect).','.mysql_real_escape_string($final,$connect).');';//\''..'\',
}
$author="dcfe700ace79a58e7b71975b3b97d58e";
function insertMatch($tourn,$session,$matchno,$connect)
{
	return 'INSERT IGNORE INTO tMatch Values(\''.mysql_real_escape_string(MatchName($tourn,$session,$matchno),$connect).'\',NULL,\''.mysql_real_escape_string(AllianceName($tourn,$session,$matchno,'R'),$connect).'\',\''.mysql_real_escape_string(AllianceName($tourn,$session,$matchno,'B'),$connect).'\');';//\''..'\',
}

function insertHappenedAt($tourn,$session,$matchno,$connect)
{
	return 'INSERT IGNORE INTO happenedAt Values(\''.mysql_real_escape_string(MatchName($tourn,$session,$matchno),$connect).'\',\''.mysql_real_escape_string($tourn,$connect).'\');';//\''..'\',
}

function insertBroke($teamno,$tourn,$session,$matchno,$color,$connect)
{
	return 'INSERT IGNORE INTO brokeWith Values(\''.mysql_real_escape_string($teamno,$connect).'\',\''.mysql_real_escape_string(AllianceName($tourn,$session,$matchno,$color),$connect).'\');';//\''..'\',
}

function insertDefensive($teamno,$tourn,$session,$matchno,$color,$connect)
{
	return 'INSERT IGNORE INTO defensiveWith Values(\''.mysql_real_escape_string($teamno,$connect).'\',\''.mysql_real_escape_string(AllianceName($tourn,$session,$matchno,$color),$connect).'\');';//\''..'\',
}
//echo "query gen functions<br/>";

queryThis(insertTeam($_POST['teamno1B'],$connect));
queryThis(insertTeamAvgScore($_POST['teamno1B'],$connect));
queryThis(insertTeam($_POST['teamno2B'],$connect));
queryThis(insertTeamAvgScore($_POST['teamno2B'],$connect));
queryThis(insertTeam($_POST['teamno3B'],$connect));
queryThis(insertTeamAvgScore($_POST['teamno3B'],$connect));
queryThis(insertTeam($_POST['teamno1R'],$connect));
queryThis(insertTeamAvgScore($_POST['teamno1R'],$connect));
queryThis(insertTeam($_POST['teamno2R'],$connect));
queryThis(insertTeamAvgScore($_POST['teamno2R'],$connect));
queryThis(insertTeam($_POST['teamno3R'],$connect));
queryThis(insertTeamAvgScore($_POST['teamno3R'],$connect));
//echo"Team check done<br/>";

queryThis(insertIsAt($_POST['teamno1B'],$_POST['tournament'],$connect));
queryThis(insertIsAt($_POST['teamno2B'],$_POST['tournament'],$connect));
queryThis(insertIsAt($_POST['teamno3B'],$_POST['tournament'],$connect));
queryThis(insertIsAt($_POST['teamno1R'],$_POST['tournament'],$connect));
queryThis(insertIsAt($_POST['teamno2R'],$_POST['tournament'],$connect));
queryThis(insertIsAt($_POST['teamno3R'],$_POST['tournament'],$connect));
queryThis(insertIsAt($_POST['teamno3R'],$_POST['tournament'],$connect));
//echo"Team location done<br/>";

queryThis(insertAlliance($_POST['teamno1B'],$_POST['teamno2B'],$_POST['teamno3B'],$_POST['tournament'],$_POST['session'],$_POST['matchno'],'B',$connect));
queryThis(insertAlliance($_POST['teamno1R'],$_POST['teamno2R'],$_POST['teamno3R'],$_POST['tournament'],$_POST['session'],$_POST['matchno'],'R',$connect));
//echo"Alliances done<br/>";

queryThis(insertScore($_POST['rawR'],$_POST['penaltyR'],$_POST['finalR'],$_POST['tournament'],$_POST['session'],$_POST['matchno'],'R',$connect));
queryThis(insertScore($_POST['rawB'],$_POST['penaltyB'],$_POST['finalB'],$_POST['tournament'],$_POST['session'],$_POST['matchno'],'B',$connect));
//echo"Scores done<br/>";

queryThis(insertMatch($_POST['tournament'],$_POST['session'],$_POST['matchno'],$connect));
//echo"Match done<br/>";

queryThis(insertHappenedAt($_POST['tournament'],$_POST['session'],$_POST['matchno'],$connect));
//echo"Match location done<br/>";


if(strcmp($_POST['broken1B'],'on')==0)
{
	queryThis(insertBroke($_POST['teamno1B'],$_POST['tournament'],$_POST['session'],$_POST['matchno'],'B',$connect));
}
if(strcmp($_POST['broken2B'],'on')==0)
{
	queryThis(insertBroke($_POST['teamno2B'],$_POST['tournament'],$_POST['session'],$_POST['matchno'],'B',$connect));
	
}
if(strcmp($_POST['broken3B'],'on')==0)
{
	queryThis(insertBroke($_POST['teamno3B'],$_POST['tournament'],$_POST['session'],$_POST['matchno'],'B',$connect));
	
}
if(strcmp($_POST['broken1R'],'on')==0)
{
	queryThis(insertBroke($_POST['teamno1R'],$_POST['tournament'],$_POST['session'],$_POST['matchno'],'R',$connect));
	
}
if(strcmp($_POST['broken2R'],'on')==0)
{
	queryThis(insertBroke($_POST['teamno2R'],$_POST['tournament'],$_POST['session'],$_POST['matchno'],'R',$connect));
	
}
if(strcmp($_POST['broken3R'],'on')==0)
{
	queryThis(insertBroke($_POST['teamno3R'],$_POST['tournament'],$_POST['session'],$_POST['matchno'],'R',$connect));
	
}
//echo"Broken done<br/>";

if(strcmp($_POST['defense1B'],'on')==0)
{
	queryThis(insertDefensive($_POST['teamno1B'],$_POST['tournament'],$_POST['session'],$_POST['matchno'],'B',$connect));
	
}
if(strcmp($_POST['defense2B'],'on')==0)
{
	queryThis(insertDefensive($_POST['teamno2B'],$_POST['tournament'],$_POST['session'],$_POST['matchno'],'B',$connect));
	
}
if(strcmp($_POST['defense3B'],'on')==0)
{
	queryThis(insertDefensive($_POST['teamno3B'],$_POST['tournament'],$_POST['session'],$_POST['matchno'],'B',$connect));
	
}
if(strcmp($_POST['defense1R'],'on')==0)
{
	queryThis(insertDefensive($_POST['teamno1R'],$_POST['tournament'],$_POST['session'],$_POST['matchno'],'R',$connect));
	
}
if(strcmp($_POST['defense2R'],'on')==0)
{
	queryThis(insertDefensive($_POST['teamno2R'],$_POST['tournament'],$_POST['session'],$_POST['matchno'],'R',$connect));
	
}
if(strcmp($_POST['defense3R'],'on')==0)
{
	queryThis(insertDefensive($_POST['teamno3R'],$_POST['tournament'],$_POST['session'],$_POST['matchno'],'R',$connect));
	
}
//echo"Defense done<br/>";

//sleep(1);

updateStats($_POST['teamno1B']);
updateStats($_POST['teamno2B']);
updateStats($_POST['teamno3B']);
updateStats($_POST['teamno1R']);
updateStats($_POST['teamno2R']);
updateStats($_POST['teamno3R']);
//echo"Stats done<br/>";


mysql_close();
session_start();
$_SESSION['lastm']=MatchName($_POST['tournament'],$_POST['session'],$_POST['matchno']);
$_SESSION['lastt']=$_POST['tournament'];
$_SESSION['nextno']=intval($_POST['matchno'])+1;
header( 'Location: frcScoutbeta.php' ) ;
?>