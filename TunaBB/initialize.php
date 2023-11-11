<?php
include_once("config.php");
include_once("functions.php");
?>

<html>

<head>
    <title>TunaBB Initialization</title>
    <meta charset="utf-8">
</head>

<style>
    code{
        color: red;
    }
</style>

<body>
    <h1>TunaBB Initialization</h1>

    <h2>Instructions</h2>
    <p>
        You must have <code>sqlite3</code> installed in your PHP installation for this script to work.
    </p>
    <p>
        Install it (e.g., <code>sudo apt-get install php-sqlite3</code>) and then enable it in your php.ini file.
    </p>
    <p>
        Make sure to <b>chown</b>, <b>chgrp</b> and <b>chmod</b> the TunaBB folder (the directory containing this file, currently <code><?php echo __DIR__; ?></code>) according to your web server's user.
    </p>
    <p>
        For example, if you are using Apache on Ubuntu:
    </p>
    <ul>
        <li><code>sudo chown www-data:www-data <?php echo __DIR__; ?></code></li>
        <li><code>sudo chgrp www-data <?php echo __DIR__; ?></code></li>
        <li><code>sudo chmod 775 <?php echo __DIR__; ?></code></li>
    </ul>
    <p>
        Make sure to <b>create</b>, <b>chown</b>, <b>chgrp</b> and <b>chmod</b> the database directory (currently set to <code><?php echo $tunabb_directory; ?></code> in <code>config.php</code>) according to your web server's user.
    </p>
    <p>
        For example, if you are using Apache on Ubuntu:
    </p>
    <ul>
        <li><code>sudo mkdir <?php echo $tunabb_directory; ?></code></li>
        <li><code>sudo chown www-data:www-data <?php echo $tunabb_directory; ?></code></li>
        <li><code>sudo chgrp www-data <?php echo $tunabb_directory; ?></code></li>
        <li><code>sudo chmod 775 <?php echo $tunabb_directory; ?></code></li>
    </ul>
        
    <p>
        Once everything is properly configured, it would be <b>wise</b> to <b>delete</b> this file.
    </p>

    <hr>
    <h1>Results</h1>

    <div style="margin: 0px 10px; padding: 0px 10px; background-color: #FCE4EC; color: black; display: inline-block;">
        <?php

        function message($msg)
        {
            echo ("<div style='margin: 10px 0px;'>$msg</div>");
        }

        function error($msg)
        {
            message($msg);
            exit(1);
        }

        ini_set('display_errors', '1');
        ini_set('display_startup_errors', '1');
        error_reporting(E_ALL);

        // Create directory for avatars
        message("Creating <code>$avatarsPath</code> directory...");
        if (!file_exists($avatarsPath)) {
            mkdir($avatarsPath, 0775, true);
            if (file_exists($avatarsPath)) {
                message("- Created directory <code>$avatarsPath</code>.");
            } else {
                error("- Error creating the <code>$avatarsPath</code> directory.");
            }
        } else {
            message("- <code>$avatarsPath</code> directory already exists. Skipping.");
        }

        // Create directory for icons
        message("Creating <code>$iconsPath</code> directory...");
        if (!file_exists($iconsPath)) {
            mkdir($iconsPath, 0775, true);
            if (file_exists($iconsPath)) {
                message("- Created directory <code>$iconsPath</code>.");
            } else {
                error("- Error creating the <code>$iconsPath</code> directory.");
            }
        } else {
            message("- <code>$iconsPath</code> directory already exists. Skipping.");
        }

        // Check if database already exists
        message("Creating Database...");
        if (file_exists($databasePath)) {
            message("- The database was already initialized at <code>$databasePath</code>. Nothing changed.");
        } else {
            // Initialize database and tables
            $db = new SQLite3($databasePath);

            // SQL to create 'user' table
            $createQueryUser = "
        CREATE TABLE IF NOT EXISTS user (
            username VARCHAR(16) PRIMARY KEY NOT NULL,
            password VARCHAR(64) NOT NULL,
            avatar VARCHAR(128),
            message_count INT DEFAULT 0,
            date_joined DATETIME DEFAULT CURRENT_TIMESTAMP
        );
    ";

            // SQL to create 'category' table
            $createQueryCategory = "
        CREATE TABLE IF NOT EXISTS category (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name VARCHAR(64) NOT NULL,
            icon VARCHAR(128),
            topics INT DEFAULT 0 NOT NULL,
            posts INT DEFAULT 0 NOT NULL
        );
    ";

            // SQL to create 'message' table
            $createQueryMessage = "
        CREATE TABLE IF NOT EXISTS message (
            category INT NOT NULL,
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            topic_id INT,
            title VARCHAR(128),
            contents TEXT NOT NULL,
            author VARCHAR(16) NOT NULL,
            date_created DATETIME DEFAULT CURRENT_TIMESTAMP,
            last_reply_author VARCHAR(16),
            last_reply_date DATETIME,
            reply_count INT DEFAULT 0 NOT NULL,
            FOREIGN KEY (category) REFERENCES category(id),
            FOREIGN KEY (author) REFERENCES user(username)
        );
    ";

            // Execute the queries
            $db->exec($createQueryUser);
            $db->exec($createQueryCategory);
            $db->exec($createQueryMessage);

            message("- Database initialized successfully at at <code>$databasePath</code>.");
        }
        message("Success! Welcome to TunaBB!");

        ?>
    </div>
</body>

</html>