<?php
include_once("functions.php");

// Category ID
if (!isset($_GET["id"])) {
    header("Location: index.php");
    exit(0);
}
$category_id = $_GET["id"];

// Category Page
$page = 1;
if (isset($_GET["page"])) {
    if (is_numeric($_GET["page"])) {
        $page = $_GET["page"];
    }
}

// Fetch Category Data
$db = database_connect();
$stmt = $db->prepare('SELECT * FROM category WHERE id = :id');
$stmt->bindValue(':id', $category_id, SQLITE3_INTEGER);
$result = $stmt->execute();
$row = $result->fetchArray(SQLITE3_ASSOC);
if (!$row) {
    header("Location: index.php");
    exit(0);
}
// Get Category Icon
if ($row["icon"]) {
    $icon = $iconsPath . "/" . $row["icon"];
} else {
    $icon = $fallback_category_icon;
}

$page_title = $row["name"];
include_once("header.php");
?>

<!-- Category Title -->
<div class="title_div">
    <h1 class="main_title">
        <img id="category_logo" src="<?php echo $icon; ?>">
        <?php echo $row["name"]; ?>
    </h1>
</div>

<!-- Start New Topic Button -->
<div id="category_options_div">
    <a href="new_topic.php?category_id=<?php echo $category_id; ?>">Start New Topic</a>
</div>

<!-- Topics Area -->
<?php
$offset = ($page - 1) * $topics_per_page;
if($offset < 0) $offset = 0;
// Prepare the SQL query to select messages with a non-null title and matching category id
$stmt = $db->prepare('SELECT * FROM message WHERE topic_id IS NULL AND category = :category_id ORDER BY last_reply_date DESC, id DESC LIMIT :topic_count OFFSET :offset');
$stmt->bindValue(':category_id', $category_id, SQLITE3_INTEGER);
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
?>
    <div class="topic_entry">
        <img class="icon_topic" src="images/icon_page.png">
        <a href="view_topic.php?id=<?php echo $row["id"]; ?>" class="topic_title_link"><?php echo $row["title"]; ?></a> (<?php echo $reply_count; ?> <?php if ($reply_count != 1) echo "Replies";
                                                                                                            else echo "Reply"; ?>)
        <br>
        <span class="topic_info">Started by <a href="user.php?name=<?php echo $row["author"]; ?>"><?php echo format_username($row["author"]); ?></a> (<?php echo $creation_date; ?>)
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
    $stmt = $db->prepare('SELECT COUNT(*) as count FROM message WHERE topic_id IS NULL AND category = :category_id');
    $stmt->bindValue(':category_id', $category_id, SQLITE3_INTEGER);
    $results = $stmt->execute();
    $topiccount = $results->fetchArray()["count"];
    $page_count = max(1, ceil($topiccount / $topics_per_page));
    ?>
    Page <?php echo $page; ?> of <?php echo $page_count; ?>:
    <?php if ($page > 1) {
    ?>
        <a href="category.php?id=<?php echo $category_id; ?>&page=<?php echo $page - 1; ?>">« Previous</a>
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
            <a href="category.php?id=<?php echo $category_id; ?>&page=<?php echo $i; ?>"><?php echo $i; ?></a>
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
        <a href="category.php?id=<?php echo $category_id; ?>&page=<?php echo $page + 1; ?>">Next »</a>
    <?php
    }
    ?>
</div>

<!-- Footer -->
<?php
include_once("footer.php");
?>