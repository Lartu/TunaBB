<?php
function failwitherror($message)
{
    header("Location: create_category.php?error=" . urlencode($message));
    exit();
}

function success($message)
{
    header("Location: create_category.php?message=" . urlencode($message));
    exit();
}

// === Get data ===================================================
if(!isset($_POST["username"]) || !isset($_POST["password"]) || !isset($_POST["cat_name"]))
{
    failwitherror("Missing fields");
}
$username = strtolower(trim($_POST["username"]));
$password = $_POST["password"];
$cat_name  = $_POST["cat_name"];

// === Check data ===================================================

if(strlen($cat_name) < 1)
{
    failwitherror("The category name you entered is too short.");
}
else if(strlen($cat_name) > 64)
{
    failwitherror("The category name you entered is too long.");
}

// === Include Directories ===================================================
include_once("functions.php");

// Check if user in the admins list
if(!is_admin($username))
{
    failwitherror("The user " . format_username($username) . " is not an administrator.");
}

// === Check if admin user exists ===================================================
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

// === Upload Avatar ===================================================
$targetDirectory = "$iconsPath/";

// Check if a file has been uploaded
if (isset($_FILES['iconfile']) && $_FILES['iconfile']['error'] != UPLOAD_ERR_NO_FILE) {
    $index = random_int(0, 9999999);
    $uploaded_filename = "$index".basename($_FILES["iconfile"]["name"]);
    $imageFileType = strtolower(pathinfo($uploaded_filename, PATHINFO_EXTENSION));
    $targetFile = "$targetDirectory/$uploaded_filename" ;

    // Validate file size - 1MB maximum
    if ($_FILES["iconfile"]["size"] > 102400) {
        failwitherror("Sorry, your file is too large. Maximum size allowed is 100KiB.");
    }

    // Allow certain file formats
    if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
        && $imageFileType != "gif" && $imageFileType != "bmp") {
            failwitherror("Sorry, only JPG, JPEG, PNG, BMP & GIF files are allowed.");
    }

    // Check if the uploads directory exists, if not create it
    if (!file_exists($targetDirectory)) {
        echo("Icons directory not found. Please initialize TunaBB using the initialize.php script.");
    }

    // Attempt to upload the file
    if (!move_uploaded_file($_FILES["iconfile"]["tmp_name"], $targetFile)) {
        failwitherror("Sorry, there was an error uploading your file.");
    } else {
        echo "The file $uploaded_filename has been uploaded.";
    }
} else {
    // $uploaded_filename remains unset.
    echo "Icon not uploaded.";
}

// === Save to Database ===================================================

// Prepare the SQL statement
$stmt = $db->prepare('INSERT INTO category (name, icon) VALUES (:name, :icon)');

// Bind parameters to the prepared statement
$stmt->bindValue(':name', $cat_name, SQLITE3_TEXT);

// Bind avatar path if set, else bind NULL
if (isset($uploaded_filename) && $uploaded_filename != '') {
    $stmt->bindValue(':icon', $uploaded_filename, SQLITE3_TEXT);
} else {
    $stmt->bindValue(':icon', null, SQLITE3_NULL);
}

// Execute the statement
$result = $stmt->execute();

if ($result) {
    success("Category <i>$cat_name</i> created successfully!");
} else {
    failwitherror("Unknown error when creating the category <i>$cat_name</i>.");
}