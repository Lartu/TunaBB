<?php
function failwitherror($error)
{
    header("Location: register.php?error=" . urlencode($error));
    exit();
}

// === Get data ===================================================
if(!isset($_POST["username"]) || !isset($_POST["password"]) || !isset($_POST["password2"]))
{
    failwitherror("Missing fields");
}
$username = strtolower(trim($_POST["username"]));
$password1 = $_POST["password"];
$password2 = $_POST["password2"];

// === Check data ===================================================

if(strlen($username) < 1)
{
    failwitherror("The username you entered is too short.");
}
else if(strlen($username) > 16)
{
    failwitherror("The username you entered is too long.");
}
else if($password1 != $password2)
{
    failwitherror("The passwords you entered don't match.");
}
else if(strlen($password1) < 1)
{
    failwitherror("Your password is too short.");
}

// === Include Directories ===================================================
include_once("functions.php");

// === Hash password ===================================================
$password_hash = password_hash($password1, PASSWORD_DEFAULT);

// === Upload Avatar ===================================================
$targetDirectory = "$avatarsPath/";

// Check if a file has been uploaded
if (isset($_FILES['avatarfile']) && $_FILES['avatarfile']['error'] != UPLOAD_ERR_NO_FILE) {
    $uploaded_filename = basename($_FILES["avatarfile"]["name"]);
    $imageFileType = strtolower(pathinfo($uploaded_filename, PATHINFO_EXTENSION));
    $new_filename = "$username.$imageFileType";
    $targetFile = "$targetDirectory/$new_filename";

    // Validate file size - 1MB maximum
    if ($_FILES["avatarfile"]["size"] > 102400) {
        failwitherror("Sorry, your file is too large. Maximum size allowed is 100KiB.");
    }

    // Allow certain file formats
    if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
        && $imageFileType != "gif" && $imageFileType != "bmp") {
            failwitherror("Sorry, only JPG, JPEG, PNG, BMP & GIF files are allowed.");
    }

    // Check if the uploads directory exists, if not create it
    if (!file_exists($targetDirectory)) {
        echo("Avatars directory not found. Please initialize TunaBB using the initialize.php script.");
    }

    // Attempt to upload the file
    if (!move_uploaded_file($_FILES["avatarfile"]["tmp_name"], $targetFile)) {
        failwitherror("Sorry, there was an error uploading your file.");
    } else {
        echo "The file $new_filename has been uploaded.";
    }
} else {
    // $new_filename remains unset.
    echo "Avatar not uploaded.";
}

// === Save to Database ===================================================

$db = database_connect();

// Prepare the SQL statement
$stmt = $db->prepare('INSERT INTO user (username, password, avatar) VALUES (:username, :password, :avatar)');

// Bind parameters to the prepared statement
$stmt->bindValue(':username', $username, SQLITE3_TEXT);
$stmt->bindValue(':password', $password_hash, SQLITE3_TEXT);

// Bind avatar path if set, else bind NULL
if (isset($new_filename) && $new_filename != '') {
    $stmt->bindValue(':avatar', $new_filename, SQLITE3_TEXT);
} else {
    $stmt->bindValue(':avatar', null, SQLITE3_NULL);
}

// Execute the statement
$result = $stmt->execute();

if ($result) {
    header("Location: register_success.php?username=$username");
} else {
    failwitherror("Sorry, the username $username is already taken.");
}