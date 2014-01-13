<?php
require_once("virtualWorld.inc.php");
require_once("user.inc.php");

$vWorld = virtualWorld::getInstance();
initWorld($vWorld);
assignUsersToRooms($vWorld);

$id=$_GET['id'];
$user_id = $_GET['user_id'];
$row = substr($id, 0, stripos('_',$id)-2);
$col = substr($id, stripos('_',$id)+2);

$desc='';
$msg = array ('status' =>'', 'msg' => '', 'users' => '', 'act' => '');
$st = '';
$roomName='';
  
if ( $vWorld->isRoom($row.'_'.$col) ) { 
  $room = $vWorld->getRoom($row.'_'.$col);
  $roomName= $room->getDesc(); 
  $usersArr = userDB::getCore()->select('users', "room_id='".$row.'_'.$col."'", NULL);
  $st = '';
  if ( $usersArr && is_array($usersArr) ) { 
    foreach ($usersArr as $user ) { 
      $st .= $user['user_name'] . ' , '; 
    }	
	$st = substr( $st, 0, strlen($st)-2);
  }	 
   
   $move = new move;
   $moveArr = $move(1, 1, $room);
   if ( is_array($moveArr) && $moveArr['ok'] ) { 
     
	 // update user room in the DB 
     $updated = userDB::getCore()->update('users', array("room_id" => $row.'_'.$col ), 'user_id='.$user_id);
  	 if ($updated ) { 
       $usersArr = userDB::getCore()->select('users', "room_id='".$row.'_'.$col."'", NULL);
       $st = '';
       if ( $usersArr && is_array($usersArr) ) { 
         foreach ($usersArr as $user ) { 
           $st .= $user['user_name'] . ' , '; 
         }	
	     $st = substr( $st, 0, strlen($st)-2);
       }
	   
       // add to user activity 
	   $activityArr=array();
       $activityArr['user_id']=$user_id;
       $activityArr['room_id']= $row.'_'.$col; 
       $activityArr['comment']='You joined room [ '.$roomName.' ]'; 	
	   $activityArr['time']=time();
  	   $added = userDB::getCore()->insert('activity',$activityArr);
	   unset($activityArr);
	   	   
	   $msg['status']= true;
	   $msg['msg']= $moveArr['msg'];
	   $msg['users']= $st;
	   $msg['act']= 'You joined room [ '.$roomName.' ]'; 
	   echo json_encode($msg);		   	
	 } else {    
	   $msg['status']= false;
	   $msg['msg']= 'Try Again!';
	   $msg['users']= $st;	
	   $msg['act']= ''; 	 
	   echo json_encode($msg);
	 }  	
   } else {
	 $msg['status']= false;
	 $msg['msg']= $moveArr['msg'];
	 $msg['users']= $st;	 
	 $msg['act']= 'You cannot join [ '.$roomName .'] room!';    
	 echo json_encode($msg);	
   }	 
} else { 

  $updated = userDB::getCore()->update('users', array("room_id" => $row.'_'.$col ), 'user_id='.$user_id);
  if ($updated ) { 
    $msg['status']= true; 
    $msg['msg']= 'Your Current Room is '.$id;

    // add to user activity 
	$activityArr=array();
    $activityArr['user_id']=$user_id;
    $activityArr['room_id']= $row.'_'.$col; 
    $activityArr['comment']='You joined room: '.$row.'_'.$col; 	
    $activityArr['time']=time();	
  	$added = userDB::getCore()->insert('activity',$activityArr); 
	unset($activityArr);
	$msg['act']= 'You joined room: '.$row.'_'.$col; 
  } else { 
	$msg['status']= false;
	$msg['msg']= 'Try Again!';
    $msg['act']= '';
  }
  $usersArr = userDB::getCore()->select('users', "room_id='".$row.'_'.$col."'", NULL);
  $st = '';
  if ( $usersArr && is_array($usersArr) ) { 
      foreach ($usersArr as $user ) { 
        $st .= $user['user_name'] . ' , '; 
      }	
	  $st = substr( $st, 0, strlen($st)-2);
  }
  $msg['users']= $st;		 
  echo json_encode($msg);	  				 
}  
