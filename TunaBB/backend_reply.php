<?php
function failwitherror($message)
{
    if (isset($_POST["topic_id"])) {
        session_start();
        if (isset($_POST["body"])) {
            $_SESSION["body"] = $_POST["body"];
        }
        $topic_id = $_POST["topic_id"];
        header("Location: view_topic.php?id=$topic_id&error=" . urlencode($message));
    } else {
        header("Location: index.php");
    }

    exit();
}

// === Get data ===================================================
if (
    !isset($_POST["username"]) || !isset($_POST["password"]) || !isset($_POST["body"])
    || !isset($_POST["topic_id"])
) {
    failwitherror("Missing fields.");
}
$username = strtolower(trim($_POST["username"]));
$password = $_POST["password"];
$body = $_POST["body"];
$topic_id  = $_POST["topic_id"];

// === Check data ===================================================

if (strlen($body) < 1) {
    failwitherror("The post you wrote is too short.");
}

// === Include Directories ===================================================
include_once("functions.php");

// === Check if user exists ===================================================
$db = database_connect();

// Prepare query
$stmt = $db->prepare('SELECT password FROM user WHERE username = :username');
$stmt->bindValue(':username', $username, SQLITE3_TEXT);

// Execute the query
$result = $stmt->execute();

// Fetching the result
if ($row = $result->fetchArray()) {
    $stored_password = $row['password'];
} else {
    failwitherror("Invalid credentials");
}

if (!password_verify($password, $stored_password)) {
    failwitherror("Invalid credentials");
}

// === Check that topic exists ===================================================
// Prepare query
$stmt = $db->prepare('SELECT category FROM message WHERE id = :topic_id');
$stmt->bindValue(':topic_id', $topic_id, SQLITE3_INTEGER);

// Execute the query
$result = $stmt->execute();

// Fetching the result
if ($row = $result->fetchArray()) {
    $category_id = $row["category"]; // At this point the category is assumed valid, since the post exists.
}else{
    failwitherror("Invalid topic.");
}

// === Save to Database ===================================================

// Prepare the SQL statement
$stmt = $db->prepare('INSERT INTO message (category, contents, author, topic_id, last_reply_date) VALUES (:category, :contents, :author, :topic_id, CURRENT_TIMESTAMP)');

// Bind parameters to the prepared statement
$stmt->bindValue(':category', $category_id, SQLITE3_INTEGER);
$stmt->bindValue(':contents', $body, SQLITE3_TEXT);
$stmt->bindValue(':author', $username, SQLITE3_TEXT);
$stmt->bindValue(':topic_id', $topic_id, SQLITE3_INTEGER);

// Execute the statement
$result = $stmt->execute();

if ($result) {
    // Increase the topic / reply count in the category
    $stmt = $db->prepare('UPDATE category SET posts = posts + 1 WHERE id = :category');
    $stmt->bindValue(':category', $category_id, SQLITE3_INTEGER);
    $result = $stmt->execute();

    // Increase the topic / reply count in the user
    $stmt = $db->prepare('UPDATE user SET message_count = message_count + 1 WHERE username = :username');
    $stmt->bindValue(':username', $username, SQLITE3_TEXT);
    $result = $stmt->execute();

    // Increase the reply count in the topic
    $stmt = $db->prepare('UPDATE message SET reply_count = reply_count + 1, last_reply_author = :username, last_reply_date = CURRENT_TIMESTAMP WHERE id = :id');
    $stmt->bindValue(':id', $topic_id, SQLITE3_INTEGER);
    $stmt->bindValue(':username', $username, SQLITE3_TEXT);
    $result = $stmt->execute();

    // Get reply number
    $stmt = $db->prepare('SELECT reply_count FROM message WHERE id = :id');
    $stmt->bindValue(':id', $topic_id, SQLITE3_INTEGER);
    $result = $stmt->execute();
    $row = $result->fetchArray();
    $reply_number = $row["reply_count"];
    header("Location: view_topic.php?id=$topic_id&post_index=$reply_number#$reply_number");

} else {
    failwitherror("Unknown error while creating the topic.");
}
