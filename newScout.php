<?php
session_start();
$_SESSION['user']=$_POST['name'];
$_SESSION['lastm']='None';

header( 'Location: frcScoutbeta.php' ) ;
?>