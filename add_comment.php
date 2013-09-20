<?php
	include ('./includes/initiate.php'); 
	
	$rev_id=$_POST['id'];
	$comment=$_POST['value'];
	$level=$_POST['level'];
	$display=$_POST['display'];
	
	$user_id=$_SESSION['user_id'];
	$query = "INSERT INTO comments (rev_id, user_id, comment, privacy, date_create)
				VALUES ($rev_id, $user_id, $comment, NOW())";
	$result = mysql_query($query);
	if ($result) {
		$replies = new replies($rev_id);
		print $replies->display($display);
	}

?>