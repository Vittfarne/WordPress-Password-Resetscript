WordPress-Password-Resetscript
==============================

A simple PHP-script to add a new administrator user to your WordPress install if you ever forget your password.

# Requirements
* PHP5.4
As this script is written using [short array syntax](http://docs.php.net/manual/en/language.types.array.php) you can only run the script using PHP5.4 or newer!
* Your database details or upload access to the wordpress install directory.


# Usage
## Automatic collection of database details
1. Upload the file to your wordpress install directory
2. navigate to the your Wordpress startpage and att /reset.php in the URL, click "Use wp-config.php", then fill in the new user account boxes.
3. Login.


## Manual
1. Upload the script to a webserver that is allowed to connect to your mysql database.
2. Navigate to the script and enter the database details (host, database name, database user, database password and table prefix) and the details for the account to be created.
3. Login with the account you created.
