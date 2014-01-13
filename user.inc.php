<?php

function assignUsersToRooms($vWorld){
  //  get All Users from users memory table
  $usersArr = userDB::getCore()->select('users', NULL, NULL);
  foreach ($usersArr as $user ) { 
    if ( $vWorld->isRoom($user['room_id']) ) { 
       $room = $vWorld->getRoom($user['room_id']);
       $userObj = new user;
       $userObj->setUserId($user['user_id']);	   
       $userObj->setName($user['user_name']);
       $userObj->setRoom($user['room_id']);
       $userObj->setAvatar($user['user_avatar']);	   
	   $room->addUser($userObj);
       $vWorld->updateRoom($user['room_id'], $room);
	   unset($room);
    }	 	 
  }
}

class user{
  public $name='';
  public $room_id='';
  public $user_id='';  
  public $avatar='';
  
  public function setName($nm=''){
    $this->name = $nm;
  }
  public function setUserId($id=''){
    $this->user_id = $id;
  }  
  public function setRoom($id=''){
    $this->room_id = $id;
  }
  public function setAvatar($avatar=''){
    $this->avatar = $avatar;
  }    
}

////////////////////////////////////////
class userDB{
   private static $_instanceCore = null;
   private static $_db;
   private static $_dbname    = 'phnWorld';
   private static $_username  = 'uname..';
   private static $_password  = 'passwd';
   private static $_hostname  = 'localhost';	   
      
   /* Using private clone function (prevents cloning) */
   private function __clone() {}   

   /* Using private constructor (prevents direct instantiation) */
   private function __construct() {
	  self::$_db = new mysqli(self::$_hostname, self::$_username, self::$_password , self::$_dbname);
	  if (self::$_db->connect_error) {
   	    $this->logger(__METHOD__. " - Connection ERROR (".self::$_dbname.")". self::$_db->connect_error );	  
        die('Connection Error (' .  self::$_db->connect_error . ') ');
	  } else { 
	    $this->logger(__METHOD__. " - Connected Successfuly.");
	  }   
   }    
	    
   //////////////////////////////////////////////	
   public static function &getCore () { 
     if ( !(self::$_instanceCore instanceof self) || is_null(self::$_instanceCore) ) {
       self::$_instanceCore = new self();
     }
	 return self::$_instanceCore;
   }

   ////////////////////////////////////
   public function filter($item=NULL) { 
      $this->logger(__METHOD__);	
	  if ( $item ) { 
        $item = self::$_db->real_escape_string(trim($item));
      }
	  return $item;	
   }

	
   ///////////////////////////////////////
   public function insert($tableName=NULL, $colsArray=NULL) {
      $this->logger(__METHOD__);	
	  if ( !isset($tableName) || !isset($colsArray) || empty($colsArray) || !is_array($colsArray) ) { 
        $this->logger(__METHOD__ . " ERROR (1): Something is wrong with $tableName OR colsArray ");		
		return false;
	  }
	  //TODO: check if table exist! 		
	  $col=" ( ";
	  $val=" VALUES ( "; 
	  foreach( $colsArray as $key => $value ) { 
		  $col .= $key . " , ";	
		  if (is_null($value)) {
		    $val .= " NULL , ";
		  } else {
		    $colsArray[$key] = $this->filter($value);
		    $val .= "'".$colsArray[$key] . "' , ";
		  }	  
	  }
	  $col = substr($col, 0, strlen($col)-2) . " ) ";
	  $val = substr($val, 0, strlen($val)-2) . " ) ";
	  $sql = "INSERT INTO " . $tableName . $col . $val;
   
  	  $ret = self::$_db->query($sql);
   	  if ($ret) {  
	    $last_id=self::$_db->insert_id;
        $this->logger(__METHOD__. " - Last Record ID - ".$last_id);			  
		return $last_id;	
	  } else { 
        $this->logger(__METHOD__. " - ERROR - ".$sql." - ".print_r(self::$_db->error,true) );	
		return false;		  
      }		
    }	
	
    ///////////////////////////////////////
	public function select($tableName=NULL, $where=NULL, $limit=1, $cols="*", $order=NULL ) { 
        $this->logger(__METHOD__);	
		if ( !isset($tableName) ) { 
          $this->logger(__METHOD__ . " ERROR in Table Name. ".$tableName );		  
		  return false;
		}  
		$sql = "SELECT ".$cols." FROM " . $tableName;
		if(isset($where)) 
		   $sql .= " WHERE " . $where;
		if(isset($order)) 
		   $sql .= " ORDER BY  " . $order;		   
		if(isset($limit))    
	       $sql .= " LIMIT " . $limit;
		   		   
		$result = self::$_db->query($sql);

   	    if ($result && $result->num_rows >0) {  
		  $rows = array();
          while ($row = $result->fetch_assoc()) {
		    if ( $limit==1 ) $rows=$row; 
            else $rows[]=$row;
          }
          $this->logger(__METHOD__. " - Number of returned rows - ".$result->num_rows );
		  return $rows;	
		} else { 
		  if ( self::$_db->error ) { 
            $this->logger(__METHOD__. " - ERROR - ".$sql);
		    $this->logger(__METHOD__. " - ERROR - ".self::$_db->error );	
		  } else { 
            $this->logger(__METHOD__. " - NOT ERROR - FOUND ".$result->num_rows);			  	
		  }	  	
		  return false;		  
		}			
    }  
		
    ///////////////////////////////////////
	public function update($tableName=NULL, $colsArray=NULL, $where=NULL, $limit=1 ) { 
        $this->logger(__METHOD__);	
		if ( !isset($tableName) || !isset($colsArray) || empty($colsArray) || !isset($where) ) { 
		  $this->logger(__METHOD__ . " something was wrong with where or cols.." );	
		  return false;
		}

		//TODO: check if table exist! 		
		$set=" SET ";
		foreach( $colsArray as $key => $value ) { 
		  if (isset($key) && isset($value) ) { 
		    $colsArray[$key] = $this->filter($value);
		    // check if value is integer or char
		    if ( is_int($colsArray[$key]) )
              $set .= "`".$key . "` = "  . $value ." , ";	  
		    else 
              $set .= "`".$key . "` = '" . $value ."' , ";	  
		   }
		}
		$set = substr($set, 0, strlen($set)-2);
		if ( $limit != NULL ) {  
		  $sql = "UPDATE " . $tableName . $set . " WHERE " . $where . " LIMIT ".$limit;
		} else { 
		  $sql = "UPDATE " . $tableName . $set . " WHERE " . $where;
		}
	  
		$this->logger(__METHOD__. " sql: " . $sql );		
		$ret = self::$_db->query($sql);

   	    if ($ret) {  
	      $affectedRows=self::$_db->affected_rows;
          $this->logger(__METHOD__. " - Number of affected rows - ".$affectedRows);			  
		  return $affectedRows;	
		} else { 
          $this->logger(__METHOD__. " - ERROR - ".$sql);	
		  $this->logger(__METHOD__. " - ERROR - ".self::$_db->error );
		  return false;		  
		}
    } 
    /////////////////////////
	function logger($str=''){
	  //TODO
	  error_log($str);
	}
   
} /* end of user class */