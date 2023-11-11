<?php
    include_once("functions.php");
    $db = database_connect();
    // Count Users
    $query = 'SELECT COUNT(*) as count FROM user';
    $usercount = $db->querySingle($query);
    // Count Topics
    $query = 'SELECT COUNT(*) as count FROM message WHERE topic_id IS NULL';
    $topiccount = $db->querySingle($query);
    // Count Messages
    $query = 'SELECT COUNT(*) as count FROM message';
    $messagecount = $db->querySingle($query);
?>

<div id="footer_div">
    <b>Stats:</b>
    Users: <?php echo $usercount;?>
    | Topics:  <?php echo $topiccount;?>
    | Messages:  <?php echo $messagecount;?>
    – Forum powered by <a href="#">TunaBB</a>.
</div>
</body>

</html>