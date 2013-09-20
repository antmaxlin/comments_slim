<?php
	include ('./includes/initiate.php'); 
	
	$rev_id=$_POST['rev_id'];
	$start=$_POST['start'];
	$replies = new replies($rev_id);
	print $replies->display(1, $start);

?>