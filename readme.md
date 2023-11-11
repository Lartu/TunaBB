![TunaBB Logo](https://github.com/Lartu/TunaBB/blob/main/small_logo.png?raw=true)

# TunaBB

**TunaBB** is a bulletin board designed from scratch to be self-contained, easy to deploy, easy to customize and easy to extend. It attempts not to use sessions, and requires users to enter their username and password every time they want to reply to a thread (a _topic_), create a new topic, etc.

The main idea behind **TunaBB** is that it should be ready-to-go (almost) as soon as you are finished
downloading it. It doesn't have any dependencies aside from what PHP already includes (except you are missing something or have an extension disabled), and it doesn't require you to use PHP package managers, or configure much stuff. Batteries included. Canned software! (_ba dum tss!_)

## Screenshots

![Screenshot 1](https://github.com/Lartu/TunaBB/blob/main/Screenshots/Screenshot1.png?raw=true)
![Screenshot 2](https://github.com/Lartu/TunaBB/blob/main/Screenshots/Screenshot2.png?raw=true)

## How to Install

To install TunaBB, clone this repository and extract the `TunaBB` in the root of your webserver (or wherever you want the forum to be). In my case this is `/var/www/html`, as I'm using Apache on Ubuntu.

Then, make sure you have the **sqlite3** extensions for PHP enabled. I had to run `sudo apt-get install php-sqlite3` to install them and uncomment `extension=sqlite3` in my **php.ini** file. You might have to do the same, or add the extension to the the ini file, or something else, depending on your distro. 

Then, create a directory **outside** of your webserver directories to store the TunaBB database. It's an SQLite database, so if you choose to store it in your webserver folder it _will_ be exposed to the internet and that's very dangerous. Don't! In my case, I chose to create `/etc/tunabb`, and that's the default directory TunaBB attempts to use.

* `sudo mkdir /etc/tunabb`

Then, you'll have to set the permissions for this folder. In my case, as I'm using Apache on Ubuntu, I had to set the permissions for the `www-data` user, that is the user Apache uses. To do so, I ran:

* `sudo chown www-data:www-data /etc/tunabb`
* `sudo chgrp www-data /etc/tunabb`
* `sudo chmod 775 /etc/tunabb`

Then, you'll have to set the permissions for the TunaBB folder in your webserver. Otherwise, TunaBB won't be able to upload user avatars and topic icons. Since I had installed TunaBB to `/var/www/html/forum`, to do so, I had to run:

* `sudo chown www-data:www-data /var/www/html/forum`
* `sudo chgrp www-data /var/www/html/forum`
* `sudo chmod 775 /var/www/html/forum`

Once that is done, point your browser to `www.your-forum-url.com/forum/initialize.php`, assuming your domain URL is `www.your-forum-url.com` and that you extracted TunaBB to a `forum` directory in the root of your webserver. Your actual values might differ. The `initialize.php` file will set up the `avatars` directory, where the user avatars are stored, the `icons` director, where the topic icons are stored, and the TunaBB database.

Once you've ran the initializer, you might want to delete `initialize.php` so nobody else can access it.

Next, you'll have to configure TunaBB! (or you may choose to do that _before_ installing TunaBB, in case you want to change the where TunaBB installs the database).

## Configuring TunaBB

Everything that's supposed to be configured by the user is in the `config.php` file. Every setting is explained within that very file.

If you want to customize the TunaBB theme, modify the `theme.css` in the `stylesheets` directory.

To add administrator accounts, add them to the `admins.php` file. The syntax is `add_admin("admin_username");`, obviously replace `admin_username` for whatever the username of the user you want to set as admin is. You can have as many administrators as you want.

To create categories, point your browser to the `admin_portal.php` panel and choose an option there.

Deleting categories, posts, topics, closing topics and banning users are planned features, but for the time being you can solve them using simple SQL queries.

## License

The TunaBB source code has been created by Lartu is released under the MIT License for everybody to use. Most images you see on the default site have been created by Lartu. These are property of Lartu and are not released under the MIT License. The default page background, however, has been shamelessly stolen from the [Dreampipe Bulletin Board](http://dreampipe.net) (that, in turn, inspired TunaBB) and the default topic icon included is the _My PC_ icon from Windows 95, property of Microsoft.
