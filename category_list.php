<?php
$query = 'SELECT * FROM category';
$results = $db->query($query);
while ($row = $results->fetchArray(SQLITE3_ASSOC)) {
    if ($row["icon"]) {
        $icon = $iconsPath . "/" . $row["icon"];
    } else {
        $icon = $fallback_category_icon;
    }
?>
    <div class="category_entry">
        <img class="icon_topic" src="<?php echo $icon; ?>">
        <a href="category.php?id=<?php echo $row["id"]; ?>" class="category_title_link"><?php echo $row["name"]; ?></a>
        <br>
        <span class="category_info">Topics: <?php echo $row["topics"]; ?> | Replies: <?php echo $row["posts"]; ?></span>
    </div>
<?php
}
