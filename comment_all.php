<?php
	include ('./functions.php');
	$page_type = 'Public';
	include ('./includes/session_check.php'); //Calls session open and session_check script.
	
	$rev_id=$_POST['rev_id'];
	$start=$_POST['start'];
	$replies = new replies($rev_id);
	print $replies->display(1, $start);

?>