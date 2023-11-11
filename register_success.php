<?php
include_once("header.php");

if(!isset($_GET["username"]))
{
    header("Location: index.php");
}else{
    $username = format_username($_GET["username"]);
}

?>

<!-- Register Title -->
<div class="title_div">
    <h1 class="main_title">
        Registration Successful
    </h1>
</div>

<!-- Welcome Message -->
<div class="paragraph_div">
    <p>
        Welcome to <?php echo $forum_name; ?>, <?php echo $username; ?>!
        <br>Keep your username and password in a safe place, as you'll need them to use the forums.
    </p>
    <p>
        <a href="index.php" class="unmarkablelink">Return to the Index</a>
    </p>
</div>

<!-- Footer -->
<?php
include_once("footer.php");
?>