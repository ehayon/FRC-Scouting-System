<?php
//error_reporting(E_ALL);
//ini_set('display_errors', 'On');
include 'themvars.php';
require_once('stats.php');

$toRem=array_keys($_POST);

$alliances=array();

foreach ($toRem as $value)
{
	$sql = "SELECT M.REDid, M.BLUEid \n"
    . "FROM tMatch M\n"
    . "WHERE M.mid='".$value."'";
	$result=queryThis($sql);
	$row=mysql_fetch_array($result);
	array_push($alliances,$row['BLUEid']);
	array_push($alliances,$row['REDid']);
}

$teams=array();

foreach ($alliances as $value)
{
	$sql = "SELECT A.t1id, A.t2id, A.t3id\n"
    . "FROM Alliance A\n"
    . "WHERE A.aid='".$value."'";
	$result=queryThis($sql);
	$row=mysql_fetch_array($result);
	array_push($teams,$row['t1id']);
	array_push($teams,$row['t2id']);
	array_push($teams,$row['t3id']);
}

//var_dump($teams);

foreach ($alliances as $value)
{
	$sql="DELETE IGNORE FROM brokeWith WHERE aid='".$value."';";
	$result=queryThis($sql);
}

foreach ($alliances as $value)
{
	$sql="DELETE IGNORE FROM defensiveWith WHERE aid='".$value."';";
	$result=queryThis($sql);
}

foreach ($alliances as $value)
{
	$sql="DELETE IGNORE FROM Score WHERE sid='".$value."';";
	$result=queryThis($sql);
}

foreach ($alliances as $value)
{
	$sql="DELETE IGNORE FROM Alliance WHERE aid='".$value."';";
	$result=queryThis($sql);
}

foreach ($toRem as $value)
{
	$sql="DELETE IGNORE FROM tMatch WHERE mid='".$value."';";
	$result=queryThis($sql);
}

foreach ($teams as $value)
{
	updateStats($value);
}

foreach ($toRem as $value)
{
	$sql="DELETE IGNORE FROM happenedAt WHERE mid='".$value."';";
	$result=queryThis($sql);
}

//remTournament($_POST['tName']);

header( 'Location: matchRem.php' ) ;
?>