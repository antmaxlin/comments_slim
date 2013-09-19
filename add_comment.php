<?php
	include ('./functions.php');
	$page_type = 'Private';
	include ('./includes/session_check.php'); //Calls session open and session_check script.
	
	$rev_id=$_POST['id'];
	$comment=escape_data(stripslashes(nl2br($_POST['value'])));
	$level=$_POST['level'];
	$display=$_POST['display'];
	
	$user_id=$_SESSION['user_id'];
	
	$data = array("rev_id"=>$rev_id, "user_id"=>$user_id, "comment"=>$comment, "privacy"=>$level, "date_create"=>'NOW()');
	$result = $mysql->insert ($table="comments",$data); // Run the query.
	if ($result) {
		memcache_stale('comments', '', '', '', $rev_id);
		$replies = new replies($rev_id);
		print $replies->display($display);
	}

?>