# Waze2db
Get alerts from Waze app's GeoJSON feed and store them in a database using CS50::query

Official web site: http://pvdpotholedb.hotwebmatter.com/

# Crowdsourcing Pothole Reporting
The city of Providence, RI has recently gained access to a real-time feed of user-generated data from the popular community-based traffic/navigation application Waze.

This user-generated data, published as a GeoJSON stream which is refreshed every two minutes, represents an untapped resource of great potential.

Currently, the Providence Department of Public Works receives phone calls and PVD311 (a service provided by PublicStuff) alerts to determine where they need to patch potholes, with no input from the Waze application.

Our goal is to make the Waze data accessible to the Providence Department of Public Works, thus leveraging an app that citizens are already using to capture data they would otherwise have to report separately if they wanted to notify the DPW.

# July 2016: TechHire Code Sprint and Hackathon
In July of 2016, a team of developers led by Joseph Curtis competed in the TechHire Hackathon at the Tech Collective in Olneyville.

Our project, the "Providence Pothole Killer," was a JavaScript web app (Node.js / Leaflet.js) demonstrating one possible interface to visualize the Waze data.

We won the competition. (Hooray!) That code is available [here](https://github.com/toklok/pvdHack), in Joe's github repo.

However, the Node.js app was only a demo. Due to the time constraints of the Hackathon, we polled the Waze data using `curl` and saved the JSON in a text file, which is what we used to create the demo map.

What we needed was a way to automate the data collection and create a persistent data store, which could be exposed to the web via a RESTful API.

# August 2016: CS50 Mashup
In August of 2016, I created the missing piece of the puzzle as part of my final project for LaunchCode's CS50x course at the University of Rhode Island.

There are two pieces to my implementation, which I will publish separately:

* `waze2db.php` is an automated data collector which polls the Waze GeoJSON feed every two minutes via `cron`.
* There is also a front-end visualization using the Google Maps API, which you can see in action [here](http://pvdpotholedb.hotwebmatter.com/map.html). I plan to publish this component separately in September.

Currently, the front-end does not offer much of an API implementation, but there are two ways that you can get at the data if you'd like to use the markers in your own map:

* [ALL THE MARKERS](http://pvdpotholedb.hotwebmatter.com/data.php): The GeoJSON stream at `http://pvdpotholedb.hotwebmatter.com/data.php` returns all of the objects from the database, although it does not display all of the fields in the `markers` table.
* [JUST A SCREENFUL](http://pvdpotholedb.hotwebmatter.com/potholes.php?ne=41.868658668750605%2C-71.29941358642577&sw=41.771304329637616%2C-71.53218641357421): The GeoJSON stream at `http://pvdpotholedb.hotwebmatter.com/potholes.php` returns only the markers that fit within the bounding box of a Google Maps object. You'll need to pass it the latitude and longitude of the northeast and southwest corners in a well-formed query string, like `?ne=41.868658668750605%2C-71.29941358642577&sw=41.771304329637616%2C-71.53218641357421`, or else it will exit with HTTP response code 400, which just looks like nothing happened. To see the raw data, try clicking the link on "JUST A SCREENFUL" above.

# Installation / Configuration

`waze2db.php` requires PHP 5.4 or higher. You'll need a relational database back-end (I'm using MySQL). And you'll need `cron`.

It is not a typical PHP web app -- it's a PHP CLI app intended to be run from the command-line interface.

Therefore, it does not need to go in a `public_html` directory --  you can set it up absolutely anywhere.

If you have your own server or VPS, you might want to put it under `/opt` somewhere.

If you are using shared hosting, you might want to install it under `$HOME/bin/`. Just be aware that you will need `cron` to run this, so you will most likely require SSH access to a shell environment. FTP alone is not good enough.

# MySQL table structure

First, you'll want to set up your database back-end. If you don't want to mess with the PHP PDO drivers, you'll use MySQL:

    CREATE DATABASE IF NOT EXISTS 'mapdata`;
    
    -- 
    -- Table structure for table `markers`
    -- 
    
    DROP TABLE IF EXISTS `markers`;
    CREATE TABLE `markers` (
      `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
      `uuid` varchar(255) NOT NULL,
      `pubMillis` bigint(13) NOT NULL,
      `lastUpdated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      `timesUpdated` int(10) NOT NULL,
      `longitude` decimal(8,6) NOT NULL,
      `latitude` decimal(8,6) NOT NULL,
      `roadType` int(1) DEFAULT NULL,
      `street` varchar(255) DEFAULT NULL,
      `country` char(2) DEFAULT NULL,
      `city` varchar(255) DEFAULT NULL,
      `type` varchar(255) DEFAULT NULL,
      `subtype` varchar(255) DEFAULT NULL,
      `magvar` int(3) DEFAULT NULL,
      `reportDescription` varchar(255) DEFAULT NULL,
      `reportRating` int(1) DEFAULT NULL,
      `confidence` int(1) DEFAULT NULL,
      `reliability` int(1) DEFAULT NULL,
      PRIMARY KEY (`id`),
      UNIQUE KEY `locationUnique` (`longitude`,`latitude`,`pubMillis`)
    ) ENGINE=InnoDB;

It's worth noting that I plan to change the database structure a bit in the near future, since `lastUpdated` and `timesUpdated` don't do anything particularly useful. Also, the composite `UNIQUE KEY` constraint will soon be replaced with a simpler `UNIQUE KEY` -- since the Waze GeoJSON contains a `uuid` field which is a unique identifier for each alert. But that's the table structure that works with this version of the data collector.

# config.json

Next, you'll need to edit `config.json` to provide authentication credentials for your MySQL database.

Replace the `**TODO**` after `"name":` with the name of your database. (In the example above, it is `mapdata`.)

Replace the `**TODO**` after `"username":` with the username of your database user.

Replace the `**TODO**` after `"password":` with the password of your database user.

# cron table setup (and peculiarities)

`cron` is pretty well-documented. Basically, you can edit your `crontab` with `crontab -e`, and list it with `crontab -l`. For more info, `man crontab` may be helpful. Or try Google. :)

Be aware that `cron` does not execute commands in the usual shell environment, so it requires absolute pathnames in the `crontab`. You can't use a path like `$HOME/bin/waze2db.php`. And you can't start the script with a "shebang" line like `#!/usr/bin/env php` because that requires `env` to resolve the path to the PHP interpreter. Instead, you want a `crontab` entry like this:

    */2 * * * * /usr/local/bin/php54 /home/webothmatter/cs50/bin/waze2db.php >> /home/webothmatter/cs50/bin/log/waze2db.log

Or, more generally:

    */2 * * * * /path/to/php /path/to/waze2db.php >> /path/to/waze2db.log

# Logging to waze2db.log

In its current incarnation, `waze2db.php` doesn't do proper logging. Instead, every two minutes, it echoes a status message with a timestamp to `STDOUT`, which we redirect to a log file via the `>>` redirector in the `crontab` entry.

The log file is primarily useful _only_ for testing that the script is functioning. If you don't want to continue logging after you've got things set up, you can edit your `crontab` entry to remove the `>>` redirector and everything after it:

    */2 * * * * /path/to/php /path/to/waze2db.php
    
Or, better yet, redirect `STDOUT` to `/dev/null`:

    */2 * * * * /path/to/php /path/to/waze2db.php >> /dev/null

# CS50::query
https://manual.cs50.net/library/#php

David J. Malan at Harvard has written a very friendly front-end to PHP's PDO, or [PHP Data Objects](http://php.net/manual/en/book.pdo.php).

Since both the back-end data store (this repository) and the front-end map visualization (another repo, coming soon!) are partially based on code that I originally wrote for my CS50 coursework, I used the CS50::query interface to PDO.

I didn't want to rewrite all of the database queries if I could help it, so I moved the code out of CS50's IDE and into my own web hosting without modification. Immediately, it broke.

That's when I discovered that CS50::query also requires [CS50 ID](https://manual.cs50.net/id/), which checks to make certain that you are a Harvard student or affiliate before allowing you to use the library.

Luckily, David Malan has licensed his software under the very permissive Open Source [BSD 3-Clause License](http://www.opensource.org/licenses/BSD-3-Clause), which says, in part:

     * Redistribution and use in source and binary forms, with or without
     * modification, are permitted provided that the following conditions are
     * met:
     *
     * * Redistributions of source code must retain the above copyright notice,
     *   this list of conditions and the following disclaimer.
     * * Redistributions in binary form must reproduce the above copyright
     *   notice, this list of conditions and the following disclaimer in the
     *   documentation and/or other materials provided with the distribution.
     * * Neither the name of CS50 nor the names of its contributors may be used
     *   to endorse or promote products derived from this software without
     *   specific prior written permission.

"With or without modification" sounded good to me, so I have removed the parts of CS50::query that require JanRain's OpenID library. It works now!

Also, this is a good time for me to note that nobody from CS50 (neither David J. Malan, nor Rob Bowden, nor Zamyla Chan, nor Doug Lloyd, nor any other Harvard affiliate) has endorsed or promoted `waze2db.php` in any way.

On the other hand, I enthusiastically endorse and promote CS50. It's a great class! You should [sign up and take it for free through edX](https://www.edx.org/course/introduction-computer-science-harvardx-cs50x)!
