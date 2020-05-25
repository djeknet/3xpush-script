<?php

require_once("../include/func.php");
require_once("../include/config.php");


$go = intval($_REQUEST['go']);
$i=0;
if ($go) {
$error='';
$conn = mysqli_connect($config['master_host'],$config['master_user'],$config['master_pass'],$config['master_db']) or $error = mysqli_connect_error();

    $sqlfile = 'db.sql';
    if (!file_exists($sqlfile));
    $open_file = fopen ($sqlfile, "r");
    $buf = fread($open_file, filesize($sqlfile));
    fclose ($open_file);

    $a = 0;

    while ($b = strpos($buf,";",$a+1)){
    $i++;
    $a = substr($buf,$a+1,$b-$a);
    if (!mysqli_query($conn,$a)) {
    	$error=mysqli_error($conn);
    	break;
    	}
    $a = $b;
    }
  if ($error) {
  echo 'FAIL: '.$error;
  } else {
 echo 'ok';
 }
 mysqli_close($conn);
}