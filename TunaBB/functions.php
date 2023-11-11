<?php
include_once("config.php");

// Paths and directories
$databasePath = "$tunabb_directory/forum_database.db";
$avatarsPath = 'avatars';
$iconsPath = 'icons';

// List of admins.
$admins = array();

// Return a connector to the database.
function database_connect(){
    global $databasePath;

    // Check if database file exists
    if (!file_exists($databasePath)) {
        die("Database not found. Please initialize TunaBB using the initialize.php script.");
    }
    
    // Connect to the SQLite database
    return new SQLite3($databasePath);
}

// Make an username pretty so it can be printed.
function format_username($username)
{
    return ucfirst(strtolower($username));
}

// Add an administrator to the forum.
function add_admin($username)
{
    global $admins;
    $username = strtolower($username);
    if(!is_admin($username))
    {
        $admins[] = $username;
    }
}

function is_admin($username)
{
    global $admins;
    include_once("admins.php");
    $username = strtolower($username);
    return in_array($username, $admins);
}

// Show all errors for debugging purposes
if($show_all_errors)
{
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
    error_reporting(E_ALL);
    echo "<div style='background-color: red; margin: 0px; color: white; padding: 10px;'>Error display is on. All errors will be displayed. This should be OFF for a live BB.</div>";
}