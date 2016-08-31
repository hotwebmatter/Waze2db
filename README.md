# Waze2db
Get alerts from Waze app's GeoJSON feed and store them in a database using CS50::query

# Crowdsourcing Pothole Reporting
The city of Providence, RI has recently gained access to a real-time feed of user-generated data from the popular community-based traffic/navigation application Waze.

This user-generated data, published as a GeoJSON stream which is refreshed every two minutes, represents an untapped resource of great potential.

Currently, the Providence Department of Public Works receives phone calls and PVD311 (a service provided by PublicStuff) alerts to determine where they need to patch potholes, with no input from the Waze application.

Our goal is to make the Waze data accessible to the Providence Department of Public Works, thus leveraging an app that citizens are already using to capture data they would otherwise have to report separately if they wanted to notify the DPW.

# July 2016: TechHire Code Sprint and Hackathon
In July of 2016, a team of developers led by Joseph Curtis competed in the TechHire Hackathon at the Tech Collective in Olneyville.

Our project, the "Providence Pothole Killer," was a demo web app (Node.js / Leaflet.js) showing one possible interface to visualize the Waze data.

We won the competition. (Hooray!) That code is available [here](https://github.com/toklok/pvdHack), in Joe's github repo.

However, the Node.js app was only a demo. Due to the time constraints of the Hackathon, we polled the Waze data using `curl` and saved the JSON in a text file, which is what we used to create the demo map.

What we needed was a way to automate the data collection and create a persistent data store, which could be exposed to the web via a RESTful API.

# August 2016: CS50 Mashup
In August of 2016, I created the missing piece of the puzzle as part of my final project for LaunchCode's CS50x course at the University of Rhode Island.

There are two pieces to my implementation, which I will publish separately:

* `waze2db.php` is an automated data collector which polls the Waze GeoJSON feed every two minutes via `cron`.
* There is also a front-end visualization using the Google Maps API, which you can see in action [here](http://pvdpotholedb.hotwebmatter.com/map.html). I plan to publish this component separately in September.

# Installation / Configuration

`waze2db.php` requires PHP 5.4 or higher. You'll need a relational database back-end (I'm using MySQL). And you'll need `cron`.

It is not a typical PHP web app -- it's a PHP CLI app intended to be run from the command-line interface.

Therefore, it does not need to go in a `public_html` directory --  you can set it up absolutely anywhere.

If you have your own server or VPS, you might want to put it under `/opt` somewhere.

If you are using shared hosting, you might want to install it under `$HOME/bin/`. Just be aware that you will need `cron` to run this, so you will most likely require SSH access to a shell environment. FTP alone is not good enough.
