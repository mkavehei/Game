<?php
require_once("virtualWorld.inc.php");
require_once("command.inc.php");
require_once("user.inc.php");

// get input
$user_id = $_POST['user_id'];
$command = $_POST['command'];

// initialize
$vWorld = virtualWorld::getInstance();
initWorld($vWorld);
$desc='';
$st = '';
$msg = array ('status' =>'', 'msg' => '', 'act' => '');
$msg['status']= true; 
$msg['msg']= '';

// prepare
$userArr = userDB::getCore()->select('users', "user_id=".$user_id);
$room_id = $userArr['room_id']; 
$room = $vWorld->getRoom($room_id);
$userObj = new user;
$userObj->setUserId($userArr['user_id']);	   
$userObj->setName($userArr['user_name']);
$userObj->setRoom($userArr['room_id']);
$userObj->setAvatar($userArr['user_avatar']);	
	   
// get action class	   
$action =  actionFactory::factory($command, $userObj);

if ( $action ) { 		 
  echo 'acted:  '.$action->action();
} else {
  echo "error"; 
}  
