<?php
include ('./includes/initiate.php'); 
include ('./comment_class.php');
$rev_id=$_POST['id'];
$comment=mysql_escape_string(trim(stripslashes(nl2br($_POST['value']))));
$level=$_POST['level'];
$display=$_POST['display'];
$user_id=$_SESSION['user_id'];
$query = "INSERT INTO comments (rev_id, user_id, comment, privacy, date_create) VALUES ('$rev_id', '$user_id', '$comment', '$level', NOW())";
$result = mysql_query($query);
if ($result) {
	$replies = new replies($rev_id);
	print $replies->display($display);
} else {
	print "Insert to database failed.";
}
?>