<?php
function failwitherror($message)
{
    if (isset($_POST["category_id"])) {
        session_start();
        if (isset($_POST["body"])) {
            $_SESSION["body"] = $_POST["body"];
        }
        if (isset($_POST["title"])) {
            $_SESSION["title"] = $_POST["title"];
        }
        $cat_id = $_POST["category_id"];
        header("Location: new_topic.php?category_id=$cat_id&error=" . urlencode($message));
    } else {
        header("Location: index.php");
    }

    exit();
}

// === Get data ===================================================
if (
    !isset($_POST["username"]) || !isset($_POST["password"])
    || !isset($_POST["title"]) || !isset($_POST["body"])
    || !isset($_POST["category_id"])
) {
    failwitherror("Missing fields.");
}
$username = strtolower(trim($_POST["username"]));
$password = $_POST["password"];
$title = $_POST["title"];
$body = $_POST["body"];
$category_id  = $_POST["category_id"];

// === Check data ===================================================

if (strlen($title) < 1) {
    failwitherror("The title you entered is too short.");
} else if (strlen($title) > 64) {
    failwitherror("The title you entered is too long.");
} else if (strlen($body) < 1) {
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

// === Check that category exists ===================================================
// Prepare query
$stmt = $db->prepare('SELECT id FROM category WHERE id = :id');
$stmt->bindValue(':id', $category_id, SQLITE3_INTEGER);

// Execute the query
$result = $stmt->execute();

// Fetching the result
if (!($row = $result->fetchArray())) {
    failwitherror("Invalid category.");
}

// === Save to Database ===================================================

// Prepare the SQL statement
$stmt = $db->prepare('INSERT INTO message (category, title, contents, author, last_reply_date) VALUES (:category, :title, :contents, :author, CURRENT_TIMESTAMP)');

// Bind parameters to the prepared statement
$stmt->bindValue(':category', $category_id, SQLITE3_INTEGER);
$stmt->bindValue(':title', $title, SQLITE3_TEXT);
$stmt->bindValue(':contents', $body, SQLITE3_TEXT);
$stmt->bindValue(':author', $username, SQLITE3_TEXT);

// Execute the statement
$result = $stmt->execute();

if ($result) {
    $topic_id = $db->lastInsertRowID();

    // Increase the topic / reply count in the category
    $stmt = $db->prepare('UPDATE category SET topics = topics + 1, posts = posts + 1 WHERE id = :category');
    $stmt->bindValue(':category', $category_id, SQLITE3_INTEGER);
    $result = $stmt->execute();

    // Increase the topic / reply count in the user
    $stmt = $db->prepare('UPDATE user SET message_count = message_count + 1 WHERE username = :username');
    $stmt->bindValue(':username', $username, SQLITE3_TEXT);
    $result = $stmt->execute();

    header("Location: view_topic.php?id=$topic_id");
} else {
    failwitherror("Unknown error while creating the topic.");
}
