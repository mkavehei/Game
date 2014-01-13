<?php
require_once("user.inc.php");

// tell <person_name> <dialog> to person_name in the same room you are
// say <dialog> to everyone in chat room
// yell <dialog> across the entire world
 
interface commandSpec{
  function getTeller();
  function getListener();  
  function getDialog();
  function action();
}

/* Say Class */
class say implements commandSpec{
  private $_teller_id=0;
  private $_listener_id=0;
  private $_dialog='';
  private $_user=NULL;
  private $_action='';
    
  public function __construct($commandArr, $user){
     $this->_teller_id = $commandArr['teller'];
	 $this->_listener_id = $commandArr['listener'];
	 $this->_dialog = $commandArr['dialog'];
	 $this->_action = $commandArr['action'];
	 $this->_user = $user;
  }
  
  public function getTeller(){
     return $this->_teller_id;
  }
  public function getListener(){
    return $this->_listener_id; 
  }  
  public function getAction(){
    return $this->_action;
  }  
  public function getDialog(){
    return $this->_dialog;
  }
  public function action(){
    // say everyone in the same room
	$listenerArr = userDB::getCore()->select('users', "room_id='".$this->_user->room_id."'", NULL);
	if ( $listenerArr && is_array($listenerArr) ) { 
	   foreach ( $listenerArr as $ls ) {
	     $listener_id = $ls['user_id'];
	     // insert into dialogs table
	     $activityArr=array();
         $activityArr['teller_id']=$this->_user->user_id;
         $activityArr['room_id']= $this->_user->room_id; 
         $activityArr['comment']=$ls['user_name'].', '.$this->_user->name.' say '.$this->_dialog. ' to everyone!'; 	
	     $activityArr['command']='say';
	     $activityArr['dialog']=$this->_dialog;
	     $activityArr['listener_id']=$listener_id;
	     $activityArr['time']=time();
  	     $added = userDB::getCore()->insert('dialogs',$activityArr);	
	     unset($activityArr); 
	   }	  
	   return true;
	} else { 
	   return false;
	} 
  }
}

/* Tell Class */
class tell implements commandSpec{
  private $_teller_id=0;
  private $_listener_id=0;
  private $_dialog='';
  private $_user=NULL;
  private $_action='';
  
  public function __construct($commandArr, $user){
     $this->_teller_id = $commandArr['teller'];
	 $this->_listener_id = $commandArr['listener'];
	 $this->_dialog = $commandArr['dialog'];
	 $this->_action = $commandArr['action'];
	 $this->_user = $user;
  }
  public function getAction(){
    return $this->_action;
  }  
  public function getTeller(){
     return $this->_teller_id;
  }
  public function getListener(){
    return $this->_listener_id; 
  }  
  public function getDialog(){
    return $this->_dialog;
  }
  public function action(){
    // tell to one user in the room
	// get the listener user_id
	$listenerArr = userDB::getCore()->select('users', "user_name like '".$this->_listener_id."'");
	if ( $listenerArr && is_array($listenerArr) && ($listenerArr['room_id'] == $this->_user->room_id) ) { 
	   $listener_id = $listenerArr['user_id'];
	   // insert into dialogs table
	   $activityArr=array();
       $activityArr['teller_id']=$this->_user->user_id;
       $activityArr['room_id']= $this->_user->room_id; 
       $activityArr['comment']=$this->_user->name.' tell '.$listenerArr['user_name'].': '.$this->_dialog; 	
	   $activityArr['command']='tell';
	   $activityArr['dialog']=$this->_dialog;
	   $activityArr['listener_id']=$listener_id;
	   $activityArr['time']=time();
  	   $added = userDB::getCore()->insert('dialogs',$activityArr);	
	   unset($activityArr);  
	   return true;
	} else { 
	   return false;
	}  

  }
}


/* yell Class */
class yell implements commandSpec{
  private $_teller_id=0;
  private $_listener_id=0;
  private $_dialog='';
  private $_user=NULL;
  private $_action='';
  
  public function __construct($commandArr, $user){
     $this->_teller_id = $commandArr['teller'];
	 $this->_listener_id = $commandArr['listener'];
	 $this->_dialog = $commandArr['dialog'];
	 $this->_action = $commandArr['action'];
	 $this->_user = $user;
  }
  
  public function getTeller(){
     return $this->_teller_id;
  }
  public function getListener(){
    return $this->_listener_id; 
  }  
  public function getDialog(){
    return $this->_dialog;
  }
  public function getAction(){
    return $this->_action;
  }  
  public function action(){
    // tell everyone in the world
    return true;
  }
}


// Action Factory Class 
class actionFactory{
  public static function factory($command, $user){
    $commandArr = self::parser($command);
	if ( !$commandArr || !is_array($commandArr) ) return false;  
	$commandArr['teller']=$user->user_id;
	switch ( strtolower($commandArr['action']) ) {
	  case 'tell':
	    $acted = new tell($commandArr, $user);
	    break;
	  case 'say':
	    $acted = new say($commandArr, $user);
	    break;
	  case 'yell':
	    $acted = new yell($commandArr, $user);
	    break;
	  default:
	    // problem: invalid action		
	}
	if ( $acted instanceof commandSpec ) { 
	  return $acted;
	} else {  
	  return false; // problem: do nothing.
	}   
  }
  /////////////////////
  private static function parser($command){
    if (!isset($command) || empty($command) ) return false;
	$command = trim($command); 
	$command = preg_replace('/\s\s+/', ' ',$command);
    $parts = explode(' ',$command);

	if ( count($parts) === 1 ) return false;
	if ( !in_array($parts[0] , $GLOBALS['validAction']) ) return false; 
	if ( count($parts) == 2 && ($parts[0] != 'yell' && $parts[0] != 'say') ) return false;
	$retArr=array();
	if ( count($parts) == 2 && $parts[0] != 'tell' ) { 
	  $retArr['action']=strtolower($parts[0]);
	  $retArr['dialog']=strtolower($parts[1]);
	  $retArr['listener']=0;
	} elseif ( count($parts) >= 3) { 
	  $retArr['action']=strtolower($parts[0]);
	  $retArr['listener']=strtolower($parts[1]);
	  array_shift($parts);
	  array_shift($parts);	 
	  $st = implode(" ", $parts);

	  $retArr['dialog']=$st;	   
	} else {
	  return false; 
	}  
	return $retArr;
  }
}


