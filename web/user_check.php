<?php
/*$con = mysql_connect("localhost","root","");
if (!$con)
  {
  die('Could not connect: ' . mysql_error());
  }

mysql_select_db("album", $con);

$result = mysql_query("SELECT * FROM users where user='".$_REQUEST['uname']."' and password='".$_REQUEST['pw']."' limit 1");

$row = mysql_fetch_array($result);
//print_r($row);exit;
*/
//if(($row['user']==$_REQUEST['uname']) && $row['password']==$_REQUEST['pw'])
if(($_REQUEST['user_name']=='epuser') && $_REQUEST['password']=='3PC1ients')
{
	session_start();
	$_SESSION['user']='epuser';
	header("location:index.php");
}
else
{
	header("location:login.php?error=1");
}

//mysql_close($con);
?>