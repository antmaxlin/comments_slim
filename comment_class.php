<?php
/** Main comment class
 * 
 * Key variable to change when setting up: $edit found in sub function display. 
 * 
 * Class is very basic- construct and display both existing comments and text box.
 * One can seperate those into seperate functions but I did not find the need.
 */

class replies {
	function __construct($rev_id = '', $rev_user = '', $anon = false, $refresh = false){
		$this->rev_id=$rev_id;
		if ($rev_user!='') {
			$this->rev_user = $rev_user;
		} else {
			$query = "SELECT user_id FROM reviews WHERE rev_id='$rev_id'";
			$result = mysql_query($query);
			$row = mysql_fetch_object($result);
			$this->rev_user = $row->user_id;
		}
		$this->anon = $anon;
		$this->refresh = $refresh;
	}
	function display ($all = 0, $start = 20){
		$edit = true; //true - allows for anonymous comments, false - signed in users only
		$rev_id = $this->rev_id; $rev_user = $this->rev_user; $count = 0;
		if (isset($_SESSION['user_id'])) {
			$self_id = $_SESSION['user_id'];
			if (($rev_user==$self_id) || (friend_check($rev_user))) { //if owner of review or friend of owner, see all
				$query = "SELECT * FROM comments WHERE rev_id='$rev_id' ORDER BY date_create DESC";
				$privacy = 1;
			} else { //see only public
				$query = "SELECT * FROM comments WHERE rev_id='$rev_id' AND privacy='0' ORDER BY date_create DESC";
			}
			$edit = true; //only allows signed in users to comment
		} else { //see only public
			$query = "SELECT * FROM comments WHERE rev_id='$rev_id' AND privacy='0' ORDER BY date_create DESC";
		}
		$result = mysql_query($query);
		$coutput="<div class='replies' id='replies_$rev_id'>\n";
		$num_row = mysql_num_rows($result);
		if (($num_row > 3) && ($all == 0)) {
			$start = 0; $limit = 3;
			$coutput.="<div><a href='javaScript:void(0)' onclick='reply_all($rev_id,0)'>View all $num_row replies</a></div>\n";
		} else {
			if ($num_row > 20){
				$previous = $start+20; $later = $start-20;
				if ($previous > $num_row) $previous = $num_row;
				if ($later < 0) $later = 0;
				if ($start == 0) {
					$coutput.="<div style='float:left'><a href='javaScript:void(0)' onclick='reply_all($rev_id, $previous)'>Previous ></a></div><div style='float:right;'>$start of $num_row</div>\n";
				} elseif ($previous == $num_row) {
					$coutput.="<div style='float:left'><a href='javaScript:void(0)' onclick='reply_all($rev_id, $later)'>< Later</a></div><div style='float:right;'>$start of $num_row</div>\n";
				} else {
					$coutput.="<div style='float:left'><a href='javaScript:void(0)' onclick='reply_all($rev_id, $later)'>< Later</a> - <a href='javaScript:void(0)' onclick='reply_all($rev_id, $previous)'>Previous ></a></div>"
					."<div style='float:right;'>$start of $num_row</div>\n";
				}
				$limit = $previous;
			} else {
				$start = 0; $limit = $num_row;
			}
		}
		while ($row = mysql_fetch_object($result)){
			if (($start <= $count) && ($count < $limit)) {
				$com_id = $row->com_id; $user_id = $row->user_id; $comment = nl2br2($row->comment); $privacy = $row->privacy; $age = age($row->date_create);
				$user = new user($user_id); $user_guid = $user->user_guid; $user_length = $user->user_length; $user_name = $user->user_name;
				$coutput.="<div class='reply' style='clear:both; overflow:auto;'>\n";
				$coutput.="<div style='float:left; width: 26px;'><a href='http://".$_SERVER['HTTP_HOST']."/user/$user_guid/$user_length/'>".$user->user_img('xs')."</div><div style='float:left; width:90%;'>".$user_name." </a>$comment ";
				$coutput.="<div style='clear:both; font-size:10px;'>$age \n";
				if ($edit) $coutput.="<a href='javascript:void(0)' onclick='document.getElementById(".'"replytext_"'."+$rev_id).value=".'"@'.$user_name.'"'."; document.getElementById(".'"privacy'.$priv.'_"'."+$rev_id).checked=true;'> Reply </a>";
				if (($self_id == $user_id) || ($self_id == $rev_user)){  //only review owner and writer of comment can delete
					$coutput.="<a href='javaScript:void(0)' onclick='reply_delete($com_id, $rev_id)'> Delete</a>\n";
				}
				$coutput.="</div>\n</div>\n";
				$coutput.="</div>\n";
			}
			$count++;
		}
		$coutput.="<div class='form_input' id='form_$rev_id' style='"; 
		if (($edit) && ($num_row>0)) {$coutput.="display:show; ";} else {$coutput.="display:none; ";}
		$coutput.="clear:both;'>\n";
		$coutput.="<div class='tip' id='tip_$rev_id' style='display:none;'>Press enter when you are done typing.</div>\n";
		$coutput.="<textarea name='replytext_$rev_id' id='replytext_$rev_id' placeholder='Add a reply...' rows='1' cols='40' onfocus='".'$("#tip_'.$rev_id.'").show()'."' onblur='".'$("#tip_'.$rev_id.'").hide()'."' onkeyup='reply(event, $rev_id)'></textarea>";
		if ($privacy == 0) {
			$coutput.="<input type='radio' id='privacy0_$rev_id' name='privacy_$rev_id' value='0' checked='checked'/>Public\n";
		} else {
			$coutput.="<input type='radio' id='privacy0_$rev_id' name='privacy_$rev_id' value='0'/>Public<input type='radio' id='privacy1_$rev_id' name='privacy_$rev_id' value='1' checked='checked'/>Friends\n";
		}
		$coutput.="<input type='hidden' id='display_$rev_id' value='$all'>\n";
		$coutput.="</div>\n";
		$coutput.="</div>\n";
		return $coutput;
	}
}

class user {
	function __construct($user_id, $refresh = false) {
		if (is_numeric($user_id)) {
		   $this->user_id = $user_id;
	   } else {
		   $this->user_id = $_SESSION['user_id'];
		   $user_id = $_SESSION['user_id'];
	   }
		$query = "SELECT * FROM users WHERE user_id='$user_id'";
		$result = mysql_query($query);
		if (mysql_num_rows($result) != 0){
			$row = mysql_fetch_object($result);
			$this->user_id = $row->user_id;
			$this->user_name = $row->user_name;
			$this->user_guid = $row->user_guid;
			$this->user_length = $row->user_length;
			$this->prim_photo_guid = $row->prim_photo_guid;
			$this->prim_photo_size = $row->prim_photo_size;
			return true;
		}
		return false;
	}
	function user_img($size = 's'){
		$user_name = $this->user_name;
		$aws_s3 = 'http://s3.amazonaws.com/weflect-users/';
		if (($this->prim_photo_guid != '') && ($this->prim_photo_size != '')) {
			$coutput.="<div class='img_$size'><img class='size_$size' alt='$user_name' src='".$aws_s3.$this->user_id."-".$this->prim_photo_guid."-".$this->prim_photo_size."-$size.jpg'/></div>\n";
		} else {
			$coutput.="<div class='img_$size'><img alt='$user_name' src='http://static.weflect.com/system/user_$size.gif'/></div>\n";
		}
		return $coutput;
	}
}

function friend_check ($user_id) {
	if (isset($_SESSION['user_id'])){
		$self_id = $_SESSION['user_id'];
		$query = "SELECT friend_id FROM friends WHERE friend_id='$self_id' AND user_id='$user_id' AND pending='0'";
		$result = mysql_query($query);
		if (mysql_num_rows($result) != 0) return true;
	}
	return false;
}

function age($date){ //function to display age of post in readable format
	$previousDate = strtotime($date);
	$currentDate = time();     // for the second date we are going to use the current Unix system time
	$nrSeconds = $currentDate - $previousDate; // subtract the previousDate from the currentDate to see how many seconds have passed between these two dates
	$nrSeconds = abs($nrSeconds); // in some cases, because of a user input error, the second date which should be smaller then the current one
	switch ($nrSeconds) {
		case ($nrSeconds < 60):
			$return = "$nrSeconds second";
			if ($nrSeconds != 1) $return.="s";
			return $return;
			break;
		case ($nrSeconds < 3600):
			$nrMinutesPassed = floor($nrSeconds / 60);
			$return = "$nrMinutesPassed minute";
			if ($nrMinutesPassed != 1) $return.="s";
			return $return;
		case ($nrSeconds < 86400):
			$nrHoursPassed = floor($nrSeconds / 3600);
			$return = "$nrHoursPassed hour";
			if ($nrHoursPassed != 1) $return.="s";
			return $return;
			break;
		case ($nrSeconds < 604800):
			$nrDaysPassed = floor($nrSeconds / 86400); // see explanations below to see what this does
			$return = "$nrDaysPassed day";
			if ($nrDaysPassed != 1) $return.="s";
			return $return;
			break;
		case ($nrSeconds < 2620800):
			$nrWeeksPassed = floor($nrSeconds / 604800); // same as above
			$return = "$nrWeeksPassed week";
			if ($nrWeeksPassed != 1) $return.="s";
			return $return;
			break;
		case ($nrSeconds < 31536000):
			$nrMonthsPassed = floor($nrSeconds / 2620800);
			$return = "$nrMonthsPassed month";
			if ($nrMonthsPassed != 1) $return.="s";
			return $return;
			break;
		default:
			$nrYearsPassed = floor($nrSeconds / 31536000); // same as above
			$return = "$nrYearsPassed year";
			if ($nrYearsPassed != 1) $return.="s";
			return $return;
			break;
	}
}

function nl2br2($content){ //stripslashes and breaks from database value
  $content = stripslashes(str_replace("\\r\\n", "<br>", $content));
  return $content;
}
?>