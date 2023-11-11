<?php
session_start();
include_once("header.php");

if (!isset($_GET["category_id"])) {
    header("Location: index.php");
    exit(0);
}
$category_id = $_GET["category_id"];
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
$category_name = $row["name"];
?>

<!-- Category Title -->
<div id="category_topic_div">
    Index / <a href="category.php?id=<?php echo $category_id; ?>"><?php echo $category_name; ?></a>
</div>

<!-- Page Title -->
<div class="title_div">
    <h1 class="main_title">
        New Topic
    </h1>
</div>

<!-- Error Message -->
<?php if (isset($_GET["error"])) { ?>
    <div id="signup_error">
        <b>Error:</b> <?php echo $_GET["error"]; ?>
        <br>Please try again.
    </div>
<?php } ?>

<!-- Topic Creation Form -->
<form method="post" action="backend_new_topic.php">
    <?php
    $body = "";
    $title = "";
    if (isset($_SESSION["body"])) {
        $body = $_SESSION["body"];
    }
    if (isset($_SESSION["title"])) {
        $title = $_SESSION["title"];
    }
    session_unset();
    session_destroy();
    ?>
    <div class="reply_controls_div">
        <h4>Title</h4>
        <input type="text" id="topic_title_input" name="title" maxlength="128" value="<?php echo $title; ?>">
        <input type="hidden" name="category_id" value="<?php echo $category_id; ?>">
    </div>
    <div class="reply_controls_div">
        <h4>Body</h4>
        <textarea id="reply_text_area" name="body"><?php echo $body; ?></textarea>
    </div>
    <div class="reply_controls_div">
        <label for="username">Username:</label>
        <input type="text" class="log_field" name="username">
        <label for="password" style="margin-left: 10px;">Password:</label>
        <input type="password" class="log_field" name="password">
    </div>
    <div class="reply_controls_div">
        <input type="submit" class="log_field" value="Post Reply">
    </div>
</form>

<!-- Footer -->
<?php
include_once("footer.php");
?>