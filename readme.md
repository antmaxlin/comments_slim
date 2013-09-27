comments slim
=====================
Guide to add slim, simple commenting to your website

setup
=====
Edit /includes/initiate.php with database info.

rev_id = id of post being commented on.
rev_user = user id of owner of the post

Database comment table:
com_id
rev_id
user_id
comment
privacy
date_create

Assumption
==========

Session storage of user_id for logged in users.
Owner of post will have a user_id.



sample
======
http://www.weflect.com/demo/comment_slim/

production example:
http://www.weflect.com/object/The+Hunger+Games/1000003501392170/26/