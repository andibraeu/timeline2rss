timeline2rss
============

a small php script to retrieve posts of facebook pages as rss feed, as facebook only provides a kind of inofficial way to get posts as rss feed.

additionally timeline2rss creates a valid title:
* there's a newline in your posts. the first newline will be taken as separator between title and content
* if there's no newline, the title will be taken from the content, you can specify the length

Prerequisites
-------------
* you need a facebook app developer account, look at https://developers.facebook.com/docs/php/gettingstarted/4.0.0
* you need an app id, an app secret and an access token
* you need a web server with php5.4 or later

Installation
------------
1. clone this repository to your web server
2. change to the directory of your clone
3. install the composer ```curl -sS https://getcomposer.org/installer | php```
4. install the dependencies with ```php composer.phar install```
5. configure the settings in ```tl2rss.php```
