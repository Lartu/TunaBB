<?php

// The name of your bulletin board
$forum_name = "Lartu's Amazing Sessionless Forum";

// The header image to use. This image will be resized to 100px of height,
// unless you've modified the default TunaBB theme.
$header_image = "images/header_image.png";

// 'Message of the Day', shown below the header image.
$motd = "A bulletin board designed from scratch to be self-contained, easy to deploy, easy to customize and easy to extend.";

// Where TunaBB installs the database when running initialize.php.
// This directory should NOT be served on the web! (e.g. NOT /var/www/html)!!
$tunabb_directory = "/etc/tunabb";

// Turn it on if you want to modify the TunaBB source code and want PHP errors to be displayed to
// your browser. Would be wise to turn off when live. If this is on, you've modified something, and
// the page is blank, you have a SYNTAX error!
$show_all_errors = false;

// The default category icon for categories you haven't uploaded an icon for.
$fallback_category_icon = "images/icon_category.png";

// The default avatar users that haven't uploaded an avatar for themselves.
$fallback_avatar = "images/default_avatar.png";

// How many topics to show on the index and on every category.
$topics_per_page = 8;

// How posts to show per page in a topic.
$posts_per_page = 10;

// How many topics to display on a user's profile page.
$user_page_max_topics = 10;