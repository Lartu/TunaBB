<?php
session_start();
include_once("functions.php");

$page = 1;
if (isset($_GET["page"])) {
    if (is_numeric($_GET["page"])) {
        $page = $_GET["page"];
    }
}

if (isset($_GET["post_index"])) {
    if (is_numeric($_GET["post_index"])) {
        $page = ceil($_GET["post_index"] / $posts_per_page);
    }
}

if (!isset($_GET["id"])) {
    header("Location: index.php");
    exit(0);
}
if (!is_numeric($_GET["id"])) {
    header("Location: index.php");
    exit(0);
}
$topic_id = $_GET["id"];


// Fetch Topic / Category Data
$db = database_connect();
$stmt = $db->prepare('SELECT message.author as m_author, message.title as m_title, category.id as c_id, category.name as c_name, category.icon as c_icon FROM message JOIN category on message.category = category.id WHERE message.id = :id');
$stmt->bindValue(':id', $topic_id, SQLITE3_INTEGER);
$result = $stmt->execute();
$row = $result->fetchArray(SQLITE3_ASSOC);
if (!$row) {
    header("Location: index.php");
    exit(0);
}
$category_name = $row["c_name"];
$category_id = $row["c_id"];
$topic_title = $row["m_title"];

// Count Topics
$stmt = $db->prepare('SELECT COUNT(*) as count FROM message WHERE topic_id = :topic_id OR id = :topic_id');
$stmt->bindValue(':topic_id', $topic_id, SQLITE3_INTEGER);
$results = $stmt->execute();
$post_count = $results->fetchArray()["count"];

if (isset($_GET["last_reply"])) {
    header("Location: view_topic.php?id=$topic_id&post_index=$post_count#$post_count");
    exit(0);
}

// Send header
$page_title = $topic_title;
include_once("header.php");

if ($row["c_icon"]) {
    $icon = $iconsPath . "/" . $row["c_icon"];
} else {
    $icon = $fallback_category_icon;
}

?>

<!-- Generate Pager String -->
<?php
$navigation_string = "";
$page_count = max(ceil($post_count / $posts_per_page), 1);
$navigation_string = "$navigation_string\nPage $page of $page_count:";

if ($page > 1) {
    $navigation_string = "$navigation_string\n<a href='view_topic.php?id=$topic_id&page=" . ($page - 1) . "'>« Previous</a>";
}
$range_min = $page - 5;
if ($range_min < 1) $range_min = 1;
$range_max = $page + 5;
if ($range_max > $page_count) $range_max = $page_count;
for ($i = $range_min; $i <= $range_max; ++$i) {
    if ($i == $page) {
        $navigation_string = "$navigation_string\n$i";
    } else {

        $navigation_string = "$navigation_string\n<a href='view_topic.php?id=$topic_id&page=$i'>$i</a>";
    }
}
if ($range_max < $page_count) {
    $navigation_string = "$navigation_string\n...";
}
if ($page < $page_count) {
    $navigation_string = "$navigation_string\n<a href='view_topic.php?id=$topic_id&page=" . ($page + 1) . "'>Next »</a>";
}
?>

<!-- Category Title -->
<div id="category_topic_div">
    Index / <a href="category.php?id=<?php echo $category_id; ?>"><?php echo $category_name; ?></a>
</div>

<!-- Recent Topics Title -->
<div class="title_div">
    <h1 class="main_title">
        <img id="category_logo" src="<?php echo $icon; ?>">
        <?php echo $topic_title; ?>
    </h1>
    by <a href="user.php?name=<?php echo $row["m_author"]; ?>"><?php echo format_username($row["m_author"]); ?></a>
</div>

<!-- Pager Area -->
<?php if ($page_count > 1) { ?>
    <div id="seeker_div_top">
        <?php echo $navigation_string; ?>
    </div>
<?php } ?>

<!-- Posts in topic -->
<?php
$offset = ($page - 1) * $posts_per_page;
if ($offset < 0) $offset = 0;
// Prepare the SQL query to select messages with a non-null title and matching category id
$stmt = $db->prepare('SELECT message.date_created as date_created, message.author as author, message.contents as body, user.message_count as m_count, user.avatar as avatar FROM message JOIN user ON message.author = user.username WHERE topic_id = :topic_id OR id = :topic_id ORDER BY id ASC LIMIT :topic_count OFFSET :offset');
$stmt->bindValue(':topic_id', $topic_id, SQLITE3_INTEGER);
$stmt->bindValue(':offset', $offset, SQLITE3_INTEGER);
$stmt->bindValue(':topic_count', $posts_per_page, SQLITE3_INTEGER);

// Execute the query
$results = $stmt->execute();
$post_index = $offset;
while ($row = $results->fetchArray(SQLITE3_ASSOC)) {
    $creation_date = (new DateTime($row["date_created"]))->format('d M Y H:i');
    if ($row["avatar"]) {
        $icon = $avatarsPath . "/" . $row["avatar"];
    } else {
        $icon = $fallback_avatar;
    }
    $post_index += 1;
    $body = $row["body"];

    // Replacements
    $body = str_replace("\n", "<br>", $body);
?>
    <div class="topic_post" id="<?php echo $post_index; ?>">
        <table>
            <tr>
                <td width="1px">
                    <img class="user_avatar" src="<?php echo $icon; ?>">
                </td>
                <td class="user_data_column" width="50%">
                    <a href="user.php?name=<?php echo $row["author"]; ?>" class=post_user_link><?php echo format_username($row["author"]); ?></a>
                    <br>Posts: <?php echo $row["m_count"]; ?>
                </td>
                <td class="post_actions_div" width="50%">
                    Posted on <?php echo $creation_date; ?></small>
                    | <a href="<?php echo "view_topic.php?id=$topic_id&post_index=$post_index#$post_index"; ?>" title="Permalink">#<?php echo $post_index; ?></a>
                </td>
            </tr>
        </table>
        <div class="post_content">
            <?php echo $body; ?>
        </div>
    </div>
<?php
}
?>

<!-- Pager Area -->
<?php if ($page_count > 1) { ?>
    <div id="seeker_div">
        <?php echo $navigation_string; ?>
    </div>
<?php } ?>

<!-- Topic Reply Controls -->
<div class="reply_controls_div">
    <h4 id="reply_title">Reply</h4>
    <form method="post" action="backend_reply.php">
        <?php
        $body = "";
        $title = "";
        if (isset($_SESSION["body"])) {
            $body = $_SESSION["body"];
        }
        session_unset();
        session_destroy();
        ?>
        <input type="hidden" name="topic_id" value="<?php echo $topic_id; ?>">
        <textarea id="reply_text_area" name="body"><?php echo $body; ?></textarea>
        <label for="username">Username:</label>
        <input type="text" class="log_field" name="username">
        <label for="password">Password:</label>
        <input type="password" class="log_field" name="password">
        <br><input type="submit" class="log_field" value="Post Reply">
    </form>
</div>


<!-- Footer -->
<?php
include_once("footer.php");
?>