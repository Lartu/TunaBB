<?php
include_once("header.php");
?>

<!-- Recent Topics Title -->
<div class="title_div">
    <h1 class="main_title">Administration Portal</h1>
</div>

<!-- Categories Area -->
<div class="paragraph_div">
    This page is meant for bulletin board administrators to configure the bulletin board.
    An administrator account is required to make any changes.
</div>

<div class="paragraph_div">
    <h4>Actions</h4>
    <ul>
        <li><a href="create_category.php" class="unmarkablelink">Create New Category</a></li>
        <li><a href="delete_category.php" class="unmarkablelink">Delete Category</a></li> (NOT IMPLEMENTED)
        <li><a href="delete_post.php" class="unmarkablelink">Delete Post</a></li> (NOT IMPLEMENTED)
        <li><a href="delete_topic.php" class="unmarkablelink">Delete Topic</a></li> (NOT IMPLEMENTED)
        <li><a href="close_topic.php" class="unmarkablelink">Close Topic</a></li> (NOT IMPLEMENTED)
        <li><a href="ban_user.php" class="unmarkablelink">Ban User</a></li> (NOT IMPLEMENTED)
    </ul>
</div>

<!-- Footer -->
<?php
include_once("footer.php");
?>