<?php
ob_start();
session_start();

$sess=check_session();

/*if($sess==false)
{exit('mm -- '.INCLUDE_PATH."/session.php");
	header("location:/login.php");
	exit;
}*/

if($sess==false)
{
    $_SESSION['qstr']='http://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
    header("location:/login.php");
}
else
{
    if(isset($_SESSION['qstr']) && $_SESSION['qstr'])
    {
        $sess_qstr = $_SESSION['qstr'] ;
        unset($_SESSION['qstr']);
	if(!empty($sess_qstr))
           header('location:'.$sess_qstr);
    }
}


function check_session()
{ 
	if(isset($_SESSION['user']))
	{
		return true;
	}
	else
	{
		return false;
   	}
	
}
?>
