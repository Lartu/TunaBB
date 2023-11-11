<?php
include_once("header.php");
$db = database_connect();

$page = 1;
if (isset($_GET["page"])) {
    if (is_numeric($_GET["page"])) {
        $page = $_GET["page"];
    }
}
?>

<!-- Recent Topics Title -->
<div class="title_div">
    <h1 class="main_title">Recent Topics</h1>
</div>

<!-- Recent Topics Area -->
<?php
$offset = ($page - 1) * $topics_per_page;
if($offset < 0) $offset = 0;
// Prepare the SQL query to select messages with a non-null title and matching category id
$stmt = $db->prepare('SELECT message.id as topic_message_id, title, reply_count, author, date_created, last_reply_author, last_reply_date, category.icon as c_icon, category.name as c_name, category.id as c_id FROM message JOIN category on message.category = category.id WHERE topic_id IS NULL ORDER BY last_reply_date DESC, message.id DESC LIMIT :topic_count OFFSET :offset');
$stmt->bindValue(':offset', $offset, SQLITE3_INTEGER);
$stmt->bindValue(':topic_count', $topics_per_page, SQLITE3_INTEGER);

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

<!-- Pager Area -->
<div id="seeker_div">
    <?php
    // Count Topics
    $query = 'SELECT COUNT(*) as count FROM message WHERE topic_id IS NULL';
    $topiccount = $db->querySingle($query);
    $page_count = max(ceil($topiccount / $topics_per_page), 1);
    ?>
    Page <?php echo $page; ?> of <?php echo $page_count; ?>:
    <?php if ($page > 1) {
    ?>
        <a href="index.php?page=<?php echo $page - 1; ?>">« Previous</a>
        <?php
    }
    $range_min = $page - 5;
    if ($range_min < 1) $range_min = 1;
    $range_max = $page + 5;
    if ($range_max > $page_count) $range_max = $page_count;
    for ($i = $range_min; $i <= $range_max; ++$i) {
        if ($i == $page) {
        ?>
            <?php echo $i; ?>
        <?php
        } else {
        ?>
            <a href="index.php?page=<?php echo $i; ?>"><?php echo $i; ?></a>
        <?php
        }
    }
    if ($range_max < $page_count) {
        ?>
        ...
    <?php
    }
    ?>
    <?php if ($page < $page_count) {
    ?>
        <a href="index.php?page=<?php echo $page + 1; ?>">Next »</a>
    <?php
    }
    ?>
</div>

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