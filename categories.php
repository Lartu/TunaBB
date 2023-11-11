<?php
include_once("header.php");
$db = database_connect();
?>

<!-- Categories Title -->
<div class="title_div">
    <h1 class="main_title">Categories</h1>
</div>

<!-- Categories Area -->
<?php
include_once("category_list.php");
?>

<!-- Footer -->
<?php
include_once("footer.php");
?>