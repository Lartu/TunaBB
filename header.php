<?php
include_once("functions.php");
if (!isset($page_title)) {
    $page_title = $forum_name;
}else{
    $page_title = "$page_title – $forum_name";
}
?>

<html>

<head>
    <title><?php echo $page_title; ?></title>
    <link rel="stylesheet" type="text/css" href="stylesheets/theme.css">
    <meta charset="utf-8">
</head>

<body>
    <a href="index.php">
        <div id="header_div">
            <img src="<?php echo $header_image; ?>">
        </div>
    </a>
    <div id="subtitle_div">
        <?php echo $motd; ?>
    </div>
    <div id="separator_div">
    </div>
    <div id="menu_div">
        <a href="index.php">Index</a> |
        <a href="categories.php">Categories</a> |
        <a href="register.php">Register</a> |
        Server Time: <?php echo date('d M Y, H:i'); ?>
    </div>