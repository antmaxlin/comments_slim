<?php
class replies {
	function __construct($rev_id='', $rev_user='', $refresh=false){
		$this->rev_id=$rev_id; $this->refresh=$refresh;
		if ($rev_user!='') {
			$this->rev_user=$rev_user;
		} else {
			$rev = new review($rev_id);
			$this->rev_user=$rev->user_id;
		}
	}
	function display ($all=0, $start=20){
		$rev_id=$this->rev_id; $rev_user=$this->rev_user; $privacy=0; $refresh=$this->refresh;
		if (isset($_SESSION['user_id'])) {
			$self_id = $_SESSION['user_id'];
			if (($rev_user==$self_id) || (friend_check($rev_user))) { //if owner of review, see all
				$key = "comments_".$rev_id;
				$row = memcache_func($key, $table="comments", $field="*", $where="rev_id='$rev_id'", $order="", $limit="", $time=86400, $refresh);
				$privacy=1;
			} else { //see only public
				$key = "comments_".$rev_id."_0";
				$row = memcache_func($key, $table="comments", $field="*", $where="rev_id='$rev_id' AND privacy='0'", $order="", $limit="", $time=86400, $refresh);
			}
			$edit=true;
		} else { //see only public
			$key = "comments_".$rev_id."_0";
			$row = memcache_func($key, $table="comments", $field="*", $where="rev_id='$rev_id' AND privacy='0'", $order="", $limit="", $time=86400, $refresh);
		}
		$coutput="<div class='replies' id='replies_$rev_id'>\n";
		$num_row=count($row); $this->num_row;
		if (($num_row > 3) && ($all==0)) {
			$start=$num_row-3; $limit=$num_row;
			$coutput.="<div><a href='javaScript:void(0)' onclick='reply_all($rev_id,20)'>View all $num_row replies</a></div>\n";
		} else {
			if ($num_row>20){
				$previous=$start+20;
				if ($previous>$num_row) $previous=$num_row;
				if ($start == $num_row) {
					$coutput.="<div style='float:right;'>$start of $num_row</div>\n";
				} else {
					$coutput.="<div style='float:left'><a href='javaScript:void(0)' onclick='reply_all($rev_id, $previous)'>View previous replies</a></div>"
					."<div style='float:right;'>$start of $num_row</div>\n";
				}
				$start=$num_row-$start; $limit=$start+20;
			} else {
				$start=0; $limit=$num_row;
			}
		}
		for ($count = $start; $count < $limit; $count++) {
			$com_id=$row[$count][com_id]; $user_id=$row[$count][user_id]; $comment=nl2br2($row[$count][comment]); $priv=$row[$count][privacy]; $age=age($row[$count][date_create]);
			$user=new user($user_id); $user_guid=$user->user_guid; $user_length=$user->user_length; $user_name=$user->user_name;
			$coutput.="<div class='reply' style='clear:both; overflow:auto;'>\n";
			$coutput.="<div style='float:left; width: 26px;'><a href='http://".$_SERVER['HTTP_HOST']."/user/$user_guid/$user_length/'>".$user->user_img('xs')."</div><div style='float:left; width:90%;'>".$user_name." </a>$comment ";
			$coutput.="<div style='clear:both; font-size:10px;'>$age \n";
			if ($edit) $coutput.="<a href='javascript:void(0)' onclick='document.getElementById(".'"replytext_"'."+$rev_id).value=".'"@'.$user_name.'"'."; document.getElementById(".'"privacy'.$priv.'_"'."+$rev_id).checked=true;'> Reply </a>";
			if ($user_id==$self_id){
				$coutput.="<a href='javaScript:void(0)' onclick='reply_delete($com_id, $rev_id)'> Delete</a>\n";	
			}
			$coutput.="</div>\n</div>\n";
			$coutput.="</div>\n";
		}
		
			$coutput.="<div class='form_input' id='form_$rev_id' style='"; 
			if (($edit) && ($num_row>0)) {$coutput.="display:show; ";} else {$coutput.="display:none; ";}
			$coutput.="clear:both;'>\n";
			$coutput.="<div class='tip' id='tip_$rev_id' style='display:none;'>Press enter when you are done typing.</div>\n";
			$coutput.="<textarea name='replytext_$rev_id' id='replytext_$rev_id' placeholder='Add a reply...' rows='1' cols='40' onfocus='".'$("#tip_'.$rev_id.'").show()'."' onblur='".'$("#tip_'.$rev_id.'").hide()'."' onkeyup='reply(event, $rev_id)'></textarea>";
			if ($privacy==0) {
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

class comment {
	function __construct($rev_id='', $rev_user='', $refresh=false){
		$this->rev_id=$rev_id;
		$this->rev_user=$rev_user;
		$this->refresh=$refresh;
	}
	
}
?>