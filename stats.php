<?php
//echo "Hello stats!<br/>";
require_once('globals.php');

function queryThis($sql)
{
	$username=$GLOBALS['username'];
	$password=$GLOBALS['password'];
	$database=$GLOBALS['database'];
	$server=$GLOBALS['server'];
	$connect=mysql_connect($server,$username,$password); 
	mysql_select_db($database) or die( "Unable to open database");
	
	$result = mysql_query($sql);
	if (!$result) 
	{
		$message  = 'Invalid query: ' . mysql_error() . "\n";
		$message .= 'Whole query: ' . $sql . 'Press back!';
		die($message);
	}
	
	mysql_close();
	
	return $result;
}
//echo "query this<br/>";

function updateStats($teamno)
{
	$username=$GLOBALS['username'];
	$password=$GLOBALS['password'];
	$database=$GLOBALS['database'];
	$server=$GLOBALS['server'];
	$connect=mysql_connect($server,$username,$password); 
	mysql_select_db($database) or die( "Unable to open database");
	$tn=mysql_real_escape_string($teamno,$connect);
	
	$sql = "select avg(S.final), avg(S.raw), avg(S.penalty)\n"
    . "FROM Score S, Alliance A \n"
    . "WHERE (A.t1id='".$tn."'\n"
    . "OR A.t2id='".$tn."'\n"
    . "OR A.t3id='".$tn."')\n"
    . "AND A.sid=S.sid";
	$result = mysql_query($sql);
	if (!$result) 
	{
		$message  = 'Invalid query: ' . mysql_error() . "\n";
		$message .= 'Whole query: ' . $sql . 'Press back!';
		die($message);
	}
	
	//echo'<br/>';
	$row = mysql_fetch_array($result);
	//var_dump($row);
	//echo'<br/>';
	$final=$row[0];
	$raw=$row[1];
	$penalty=$row[2];
	
	if($final==NULL)
	{
		$final=0;
	}
	if($raw==NULL)
	{
		$raw=0;
	}
	if($penalty==NULL)
	{
		$penalty=0;
	}
	
	$sql = "UPDATE Score\n"
    . "SET final=".$final.", raw=".$raw.", penalty=".$penalty."\n"
    . "WHERE sid='".$tn."'";
	
	$result = mysql_query($sql);
	if (!$result) 
	{
		$message  = 'Invalid query: ' . mysql_error() . "\n";
		$message .= 'Whole query: ' . $sql . 'Press back!';
		die($message);
	}
	//echo "score averages<br/>";
	$sql = "select count(A.sid)\n"
    . "FROM Alliance A \n"
    . "WHERE A.t1id='".$tn."'\n"
    . "OR A.t2id='".$tn."'\n"
    . "OR A.t3id='".$tn."'";
	
	$matchesarry=mysql_fetch_array(queryThis($sql));
	$matches = intval($matchesarry[0]);
	//echo "matches<br/>";
	
	$sql = "select count(B.aid)\n"
    . "from brokeWith B\n"
    . "where B.teamno='".$tn."';";
	
	$brokenarry=mysql_fetch_array(queryThis($sql));
	$broken = intval($brokenarry[0]);
	//echo "broken<br/>";
	
	$reliability=1-($broken/$matches);
	
	$sql = "select A.aid\n"
    . "FROM Score S, Alliance A\n"
    . "WHERE (A.t1id='".$tn."'\n"
    . "OR A.t2id='".$tn."'\n"
    . "OR A.t3id='".$tn."')\n"
    . "AND A.sid=S.sid";
	
	$allianceq=queryThis($sql);
	
	$alliances=array();
	
	while($row = mysql_fetch_array($allianceq)) 
	{
		array_push($alliances,$row['aid']);
	}
	
	$opponents=array();
	
	foreach($alliances as $alliance)
	{
		if($alliance[strlen($alliance)-1]=='B')
		{
			$alliance=str_replace('B','R',$alliance);
		}
		else if($alliance[strlen($alliance)-1]=='R')
		{
			$alliance=str_replace('R','B',$alliance);
		}
		array_push($opponents,$alliance);
	}
	
	$opcount=count($opponents);
	$opavg=0.0;
	
	foreach($opponents as $alliance)
	{
		$sql = "select S.final\n"
		. "FROM Score S\n"
		. "WHERE S.sid='".$alliance."'";
		$opfinal=mysql_fetch_array(queryThis($sql));
		$opavg+=floatval($opfinal[0]);
	}
	$opavg=(float)$opavg/(float)$opcount;
	
	$offense=((float)$final-$opavg)/$opavg;
	
	if($opcount==0)
	{
		$offense=0;
	}
	
	if($opavg==0)
	{
		$offense=0;
	}
	
	$sql = "select D.aid\n"
    . "FROM defensiveWith D\n"
    . "WHERE D.teamno='".$tn."';";
	
	$dalliancesq=queryThis($sql);
	
	//echo $sql."<br/>";
	
	$dalliances=array();
	
	while($row = mysql_fetch_array($dalliancesq)) 
	{
		//var_dump($row);
		//echo "<br/>";
		array_push($dalliances,$row['aid']);
	}
	
	$dopponents=array();
	
	foreach($dalliances as $dalliance)
	{
		if($dalliance[strlen($dalliance)-1]=='B')
		{
			$dalliance=str_replace('B','R',$dalliance);
		}
		else if($dalliance[strlen($alliance)-1]=='R')
		{
			$dalliance=str_replace('R','B',$dalliance);
		}
		array_push($dopponents,$dalliance);
	}
	
	$dopcount=count($dopponents);
	$dopavg=0.0;
	$dopfinal=array();
	
	foreach($dopponents as $dalliance)
	{
		$sql = "select S.final\n"
		. "FROM Score S\n"
		. "WHERE S.sid='".$dalliance."'";
		$dopfinal=mysql_fetch_array(queryThis($sql));
		$dopavg+=floatval($dopfinal[0]);
	}
	$dopavg=(float)$dopavg/(float)$dopcount;
	
	$defense=((float)$final-$dopavg)/$dopavg;
	
	if($dopcount==0)
	{
		$defense=0;
	}
	
	if($dopavg==0)
	{
		$defense=0;
	}
	
	$sql = "UPDATE Team\n"
    . "SET reliability=".$reliability.", offense=".$offense.", defense=".$defense."\n"
    . "WHERE teamno='".$tn."'";
	
	queryThis($sql);
	
	/*echo $sql."<br/>".$dopavg."<br/>".$dopcount."<br/>";
	var_dump($dopponents);
	echo"<br/>";
	var_dump($dalliances);
	echo"<br/>";*/
	
	mysql_close();
}

?>