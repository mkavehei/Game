<?php
// initialize
$GLOBALS = array(
  'maxRows' => 8,
  'maxCols' => 8,
  'validAction' => array("say", "tell", "yell"), 
  'world' => array(
    array ( 'row' => 1, 'col' => 3, 'type' => 1, 'desc' => 'Solid', 'capacity' => 0  , 'users' => 0 , 'msg' => '', 'img'=>'/party.jpg'), /* Solid Rooms */
    array ( 'row' => 1, 'col' => 7, 'type' => 1, 'desc' => 'Solid', 'capacity' => 0  , 'users' => 0 , 'msg' => '', 'img'=>'/death.jpg'),
    array ( 'row' => 3, 'col' => 7, 'type' => 1, 'desc' => 'Solid', 'capacity' => 0  , 'users' => 0 , 'msg' => '', 'img'=>'/scary1.jpg'),
    array ( 'row' => 4, 'col' => 1, 'type' => 1, 'desc' => 'Solid', 'capacity' => 0  , 'users' => 0 , 'msg' => 'You never Know!', 'img'=>'/death.jpg'),
    array ( 'row' => 4, 'col' => 5, 'type' => 1, 'desc' => 'Solid', 'capacity' => 0  , 'users' => 0 , 'msg' => '', 'img'=>'/spider.jpg'),
    array ( 'row' => 7, 'col' => 4, 'type' => 1, 'desc' => 'Solid', 'capacity' => 0  , 'users' => 0 , 'msg' => '', 'img'=>'/flower.jpg'),  
    array ( 'row' => 7, 'col' => 8, 'type' => 1, 'desc' => 'Solid', 'capacity' => 0  , 'users' => 0 , 'msg' => '', 'img'=>''),  
    array ( 'row' => 1, 'col' => 4, 'type' => 2, 'desc' => 'Party', 'capacity' => 10 , 'users' => 5 , 'img' => '/dance.jpg', 'msg'=>'No worry, we cannot dance either!'), /* VIP Rooms */
    array ( 'row' => 6, 'col' => 3, 'type' => 2, 'desc' => 'Zoo',   'capacity' => 6  , 'users' => 4 , 'img' => '', 'msg'=>"Danger!! Don't mess with big cats.."),
    array ( 'row' => 4, 'col' => 6, 'type' => 2, 'desc' => 'Club',  'capacity' => 5  , 'users' => 5 , 'img' => '', 'msg'=>'Come if you can dance!'),
    array ( 'row' => 3, 'col' => 1, 'type' => 2, 'desc' => 'Secret','capacity' => 5  , 'users' => 3 , 'img' => '', 'msg'=>'Oh, what have you done??!! '),   
  ), 
);

/* room type 0: transparent */
/* room type 1: solid */
/* room type 2: VIP */
///////////////////////////////////
interface roomSpec{
  function getDesc();
  function getType();
  function getCapacity();
  function getUsers();
  function getAllUsers();  
  function getRow();
  function getCol();
}
class room implements roomSpec{
  private $_desc='';
  private $_type=0;
  private $_capacity=0;
  private $_row=0;
  private $_col=0;
  private $_img='';
  private $_msg='';
  private $_users=0;
  private $_usersArr=array();
  
  function setRow($row=0){
    if ($row) $this->_row=$row;
  }
  function setCol($col=0){
    if ($col) $this->_col=$col;
  }
  function setDesc($desc=''){
    if ($desc) $this->_desc=$desc;
  }
  function setType($type=0){
    if ($type) $this->_type=$type; 
  }
  function setCapacity($capacity=0){
    if ($capacity) $this->_capacity = $capacity;
  }
  function setCurrentUsers($users=0){
    if ($users && $users<=$this->getCapacity()) $this->_users = $users;
  }  
  function setUsers($usersArr=NULL){
    $this->_usersArr = $usersArr;
  }
  function setMsg($msg=''){
    if( $msg && !empty($msg) ) $this->_msg = $msg; 
  }
  function setImg($img=''){
    if( $img && !empty($img) ) $this->_img = $img;
	// TODO: check if img file exist  
  }
  /* ------------ */
  function addUser($user){ 
    $this->_usersArr[]=$user;
	return true;
  }
  function getAllUsers(){
    return $this->_usersArr;
  }
  function getUsers(){
    return $this->_users;
  }
  function getDesc(){
    return $this->_desc;
  }
  function getType(){
    return $this->_type; 
  }
  function getCapacity(){
    return $this->_capacity;
  }
 
  function getRow(){
    return $this->_row;
  }
  function getCol(){
    return $this->_col;
  }  
  function getImg(){
    return $this->_img;
  }          
  function getMsg(){
    return $this->_msg;
  }    
  
}
/////////////////////////////////

/////////////////////////////////
class virtualWorld{
  private static $_maxRows=0;
  private static $_maxCols=0;
  private static $_instance = null;
  private static $_roomArray = array();
  /* Using private clone function (prevents cloning) */
  private function __clone(){}   
  /* Using private constructor (prevents direct instantiation) */
  private function __construct(){
    self::$_maxRows=$GLOBALS['maxRows'];
	self::$_maxCols=$GLOBALS['maxCols'];     
  }    
  /* there would be only one virtual world in this game */	
  public static function &getInstance() { 
    if ( !(self::$_instance instanceof self) || is_null(self::$_instance) ) {
      self::$_instance = new self();
    }
	return self::$_instance;
  }  
  function getMaxRows(){
    return self::$_maxRows;
  }
  function getMaxCols(){
    return self::$_maxCols;
  }	
  function addRoom($ij, $room){ 
    self::$_roomArray[$ij]=$room;
  }
  function updateRoom($ij, $room){
    self::$_roomArray[$ij]=$room;
  }
  function getRoom($ij){
    return ( isset(self::$_roomArray[$ij]) ? self::$_roomArray[$ij] : false );
  }
  function isRoom($ij){
    return isset(self::$_roomArray[$ij]);
  }	   
}


//////////////////////////
function initWorld($vWorld){
  $worldArr = $GLOBALS['world'];
  foreach ($worldArr as $rooms) { 
    $room = new room();
    $room->setRow($rooms['row']);
    $room->setCol($rooms['col']);
    $room->setType($rooms['type']);
    $room->setDesc($rooms['desc']);
    $room->setCapacity($rooms['capacity']);
    $room->setCurrentUsers($rooms['users']);
    $room->setMsg($rooms['msg']);
    $room->setImg($rooms['img']);
    $room->setUsers(array());
    $vWorld->addRoom($rooms['row'].'_'.$rooms['col'], $room);
    unset($room);
  }
  unset($worldArr);  
}


/////////////////////////////////
class move{
  private $curRow;
  private $curCol;
  private $nextRoom;
  public function __invoke($row=NULL, $col=NULL, $nextRoom){
    if ($row && $col && $nextRoom) { 
      $this->curRow = $row;
	  $this->curCol = $col;
	  $this->nextRoom = $nextRoom;
	} else {
	  return array ('ok' => false, 'msg'=>'wrong info!'); 
	} 
    $nextRow  = $nextRoom->getRow();
    $nextCol  = $nextRoom->getCol();
	if ( $nextCol == $this->curCol && $nextRow == $this->curRow ) return array ('ok' => false, 'msg'=>'The same room.'); 
	if ( $nextRow > $this->curRow ) return $this->canMove($nextRoom); // goNorth
	if ( $nextRow < $this->curRow ) return $this->canMove($nextRoom); // goSouth
	if ( $nextRow == $this->curRow ) {
	  if ( $nextCol < $this->curCol ) return $this->canMove($nextRoom); // goWest
	  if ( $nextCol > $this->curCol ) return $this->canMove($nextRoom); // goEast
	}   
  }
  private function canMove($nextRoom){
	$type     = $nextRoom->getType();
    if ( $type == 1 ) { 
	  return array ('ok' => false, 'msg'=>'Solid Room.'); 
	  /* going to Solid Room - Invalid Move */
	}
	$cap      = $nextRoom->getCapacity();
    $users    = $nextRoom->getUsers();
	if ( ( $users + 1 ) <= $cap ) return array ('ok' => true, 'msg'=>$nextRoom->getDesc().' -- '.$nextRoom->getMsg());
	return array ('ok' => false, 'msg'=>'This Room is Full. Come back later!');
  }
} // end of move class


