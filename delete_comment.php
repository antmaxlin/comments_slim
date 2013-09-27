<?php
include ('./includes/initiate.php');
include ('./comment_class.php');

$com_id=$_POST['com_id'];
$rev_id=$_POST['rev_id'];
$display=$_POST['display'];
$user_id=$_SESSION['user_id'];
$query = "DELETE FROM comments WHERE com_id=$com_id AND user_id=$user_id";
$result = mysql_query($query);
if ($result) {
	$replies = new replies($rev_id);
	print $replies->display($display);
}
?>