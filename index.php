<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>Comment - Slim</title>
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
	<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>
    <script src="./js/comment.js"></script>
</head>
<body>

<?php
include ('./includes/initiate.php');
include ('./comment_class.php');

$rev_id=233; $user_id=10; //sample information
$replies = new replies($rev_id, $user_id);  
print $replies->display();
?>

</body>
</html>