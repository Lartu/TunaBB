<?php
include_once("functions.php");

if (!isset($_GET["name"])) {
    header("Location: index.php");
    exit(0);
}

$username = trim($_GET["name"]);

// Seek User
$db = database_connect();

$stmt = $db->prepare('SELECT * FROM user WHERE username = :username');
$stmt->bindValue(':username', $username, SQLITE3_TEXT);
$result = $stmt->execute();
if ($row = $result->fetchArray()) {
    if ($row["avatar"]) {
        $icon = $avatarsPath . "/" . $row["avatar"];
    } else {
        $icon = $fallback_avatar;
    }
    $join_date = (new DateTime($row["date_joined"]))->format('d M Y H:i');
} else {
    header("Location: index.php");
    exit(0);
}

$formatted_username = format_username($username);

// Send header
$page_title = $formatted_username;
include_once("header.php");

?>

<!-- Category Title -->
<div class="title_div">
    <h1 class="main_title">User: <?php echo $formatted_username; ?></h1>
</div>

<!-- Avatar -->
<div class="paragraph_div">
    <img class="user_avatar" src="<?php echo $icon; ?>">
</div>

<!-- Information -->
<div class="paragraph_div">
    <h4>Stats</h4>
    Joined Date: <?php echo $join_date; ?>
    <br>Message Count: <?php echo $row["message_count"]; ?>
</div>

<!-- Last Topics -->
<div class="title_div">
    <h1 class="main_title">Last Topics Created by <?php echo $formatted_username; ?></h1>
</div>

<!-- Recent Topics Area -->
<?php
// Prepare the SQL query to select messages with a non-null title and matching category id
$stmt = $db->prepare('SELECT message.id as topic_message_id, title, reply_count, author, date_created, last_reply_author, last_reply_date, category.icon as c_icon, category.name as c_name, category.id as c_id FROM message JOIN category on message.category = category.id WHERE topic_id IS NULL AND author=:author ORDER BY message.id DESC LIMIT :topic_count');
$stmt->bindValue(':topic_count', $user_page_max_topics, SQLITE3_INTEGER);
$stmt->bindValue(':author', $username, SQLITE3_TEXT);

// Execute the query
$results = $stmt->execute();
while ($row = $results->fetchArray(SQLITE3_ASSOC)) {
    $reply_count = $row["reply_count"];
    $creation_date = (new DateTime($row["date_created"]))->format('d M Y H:i');
    $last_reply_author = $row["last_reply_author"];
    if ($last_reply_author) {
        $last_reply_date = (new DateTime($row["last_reply_date"]))->format('d M Y H:i');
    }
    if ($row["c_icon"]) {
        $icon = $iconsPath . "/" . $row["c_icon"];
    } else {
        $icon = $fallback_category_icon;
    }
?>
    <div class="topic_entry">
        <img class="icon_topic" src="images/icon_page.png">
        <a href="view_topic.php?id=<?php echo $row["topic_message_id"]; ?>" class="topic_title_link"><?php echo $row["title"]; ?></a> (<?php echo $reply_count; ?> <?php if ($reply_count != 1) echo "Replies";
                                                                                                            else echo "Reply"; ?>)
        <br>
        <span class="topic_info">Started by <a href="user.php?name=<?php echo $row["author"]; ?>"><?php echo format_username($row["author"]); ?></a> (<?php echo $creation_date; ?>)
        on <img class="icon_topic" src="<?php echo $icon; ?>"> <a href="category.php?id=<?php echo $row["c_id"];?>"><?php echo $row["c_name"]; ?></a>
            <?php if ($last_reply_author) { ?>
                - <a href="view_topic.php?id=<?php echo $row["topic_message_id"]; ?>&last_reply=1">last</a> reply by <a href="user.php?name=<?php echo $last_reply_author; ?>"><?php echo format_username($last_reply_author); ?></a> (<?php echo $last_reply_date; ?>)
            <?php } ?>
        </span>
    </div>
<?php
}
?>

<!-- Footer -->
<?php
include_once("footer.php");
?>