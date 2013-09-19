<?php
	include ('./functions.php');
	$page_type = 'Private';
	include ('./includes/session_check.php'); //Calls session open and session_check script.
	
	$com_id=$_POST['com_id'];
	$rev_id=$_POST['rev_id'];
	$display=$_POST['display'];
	$user_id=$_SESSION['user_id'];
	$result = $mysql->delete($table="comments",$where="com_id='$com_id' AND user_id='$user_id'");
	if ($result) {
		memcache_stale('comments', '', '', '', $rev_id);
		$replies = new replies($rev_id);
		print $replies->display($display);
	}

?>