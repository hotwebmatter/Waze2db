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

# CS50::query
https://manual.cs50.net/library/#php

David J. Malan at Harvard has written a very friendly front-end to PHP's PDO, or [PHP Data Objects](http://php.net/manual/en/book.pdo.php).

Since both the back-end data store and the front-end map visualization are partially based on code that I originally wrote for my CS50 coursework, I used the CS50::query interface to PDO.

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
