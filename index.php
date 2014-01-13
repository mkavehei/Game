<?php
require_once("virtualWorld.inc.php");
require_once("command.inc.php");
require_once("user.inc.php");


// Login using GET Method
$yourRoomId = '';
if ($_GET && $_GET['id'] ) { 
  $userId = $_GET['id'];
  $userArr = userDB::getCore()->select('users', "user_id=".$userId);
  $youAre = '';
  if ( $userArr && is_array($userArr) ) {
    $youAre = $userArr['user_name'];
	$yourRoomId = $userArr['room_id'];
    $welcomeMsg =  "<div class='tcl'><h1>Welcome ".$youAre."! You were in room ".$userArr['room_id']."</h1></div>"; 
  }	else {
     $welcomeMsg =  "<div class='tcl'><h1>user does not exist!</h1></div>";
	exit; 
  }
} else { 
  echo "Please login";
  exit;
}  	
  
  
$vWorld = virtualWorld::getInstance();
$maxRows = $vWorld->getMaxRows();
$maxCols = $vWorld->getMaxCols();
initWorld($vWorld);
assignUsersToRooms($vWorld);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title>Game | MULTIUSER DUNGEON</title>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<link rel="stylesheet" type="text/css" media="screen,print" href="/page.css" />
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>  
<script src="http://code.jquery.com/jquery-1.8.2.js"></script>
<script src="http://code.jquery.com/ui/1.9.1/jquery-ui.js"></script> 

<script type="text/javascript">/* <![CDATA[ */
$(document).ready(function(){
   var refreshId = setInterval( function() {
      $.get("/activity_backend.php?id="+<?= $userId ?>,function(data,status){
	    obj = jQuery.parseJSON(data);
		$('.activity').prepend(obj.activity);   
      });
   }, 1000);
	
  // Move Action //
  $(".col").click(function(){
    var id = $(this).attr('id');
	var param = 'id='+id+'&user_id='+<?= $userId ?>;
    $.get("/moves_backend.php?"+param,function(data,status){
	   obj = jQuery.parseJSON(data);
       $('.msg').html("<b>Message: </b>"+obj.msg);
	   $('.users').html("<b>Users: </b>"+obj.users);	
	   $('.activity').prepend("<div><b>"+obj.act+"</b></div>");   
	   if ( obj.status == true ) { 
	     $('div[id^="T_"]').css("background-color","#fff");
		 $('div[id^="T_"]').html('');
	     $('#T_'+id).css("background-color","#000");
		 $('#T_'+id).html("You are here!");
	   }	 
    });
  });

  // Command Action //
  $("#action").click(function(){
    var id = $(this).attr('id');
	var user_id=<?= $userId ?>;
	var command=$("#command").val();
	$.post('/commands_backend.php', {'user_id':user_id,'command':command}, function(data) { 
       obj = jQuery.parseJSON(data); 
    });
    
  });
    
});/*]]>*/
</script>

</head>

<body>
<?php
echo $welcomeMsg;
echo "<div id='main' class='main'>";
 echo "<div class='users'><b>Users: </b></div>";
 echo "<div class='msg'><b>Message: </b></div>";
 echo "<div class='command'><b>Command Here: </b><input type='text' value='' size='50' id='command' />   <button id='action'>go</button>";
 echo '   example: Say "Hello.." </div>';
 echo "<div class='map'>"; 
  echo "<div id='vw' class='vw'>";
  for ($row=1; $row<=$maxRows; $row++){
    echo "<div id='".$row."' class='row'>";
    for ( $col=1; $col<=$maxCols; $col++) { 
	   $typeClass = '';
	   $cap=0;
	   $desc='';
	   $type=0;
	   if ( $vWorld->isRoom($row.'_'.$col) ) { 
	     $room = $vWorld->getRoom($row.'_'.$col);
		 $type = $room->getType();
	     if ( $type === 1 ) $typeClass = " solid";
		 if ( $type === 2 ) $typeClass = " vip";    
		 $cap = $room->getCapacity();
		 $desc= $room->getDesc();
		 $users= $room->getUsers();
		 $img= $room->getImg();
	   }
	   echo "<div id='".$row."_".$col."' class='col".$typeClass."'>";
	   if ( $type === 1 ) { 
         if ( $img ) echo "<img src='".$img."' width='90' height='90' />";

	     // do nothing now!
	   } elseif ( $cap > 0 ) { 
	     echo "<div class='showRoom'>";
		 echo '<b>'.$desc."</b><br/><b>MAX: </b>".$cap;
		 echo "</div>";
 		 if ( $yourRoomId == $row."_".$col ) {	
	       echo "<div id='T_".$row."_".$col."' class='uStep black'>";
		   echo "You are here!";
		 } else { 
	       echo "<div id='T_".$row."_".$col."' class='uStep'>";
		 }
		 echo "</div>";
	   } else {
	     echo "<div class='showRoom'>";
		 echo "</div>";
 		 if ( $yourRoomId == $row."_".$col ) {	
	       echo "<div id='T_".$row."_".$col."' class='uStep black'>";
		   echo "You are here!";
		 } else { 
	       echo "<div id='T_".$row."_".$col."' class='uStep'>";
		 }  		 
		 echo "</div>";
	   }
	   echo "</div>";
    } 
    echo "</div>"; // end of row 
  } 
  echo "</div>";
  echo "<div class='dialog'>";
    echo "<div><h2>Dialog</h2></div>";
    echo "<div class='activity'></div>";
		echo "<div class='clr'></div>";
	echo "<div class='clr'></div>";
  echo "</div>";
	echo "<div class='clr'></div>";  
 echo "</div>"; 
	echo "<div class='clr'></div>";  
echo "</div>";  
      
?> 
 
  
</body>
</html>