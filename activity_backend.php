<?php
date_default_timezone_set('America/Los_Angeles');

require_once("virtualWorld.inc.php");
require_once("command.inc.php");
require_once("user.inc.php");

$user_id=$_GET['id'];
$st='';  
$list = userDB::getCore()->select('dialogs' , 'displayed IS NULL AND ( listener_id='.$user_id.' OR teller_id='.$user_id.' )', 1, '*', ' time DESC' );
if ( $list && is_array($list) ) { 
  $time = date("y-m-d H:i:s", $list['time']);
  $st   = "<div>".$time." - ".$list['comment']."</div>";
  sleep(3);
  // update dialogs table for which was displayed, set it off.
  $updated = userDB::getCore()->update('dialogs', array("displayed" => 1 ),'id='.$list['id']);
  echo json_encode( array('activity' => $st) );
} else { 
  echo json_encode( array('activity' => NULL) );
}