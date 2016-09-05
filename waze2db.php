#!/usr/local/bin/php54
<?php

    // TODO: import potholes from Waze GeoJSON feed into mySQL db

    // display errors, warnings, and notices
    ini_set("display_errors", true);
    error_reporting(E_ALL);

    echo date('Y-m-d H:i:s') . ": ";
    $count = 0;

    // CS50 Library
    require(__DIR__ . "/vendor/library50-php-5/CS50/CS50.php");
    CS50::init(__DIR__ . "/config.json");

    // read the data from Waze GeoJSON
    $url = 'https://na-georss.waze.com/rtserver/web/TGeoRSS?tk=ccp_partner&ccp_partner_name=Providence&format=JSON&types=alerts&polygon=-71.440487,41.866467;-71.374741,41.858029;-71.375427,41.828744;-71.395512,41.810450;-71.373367,41.785033;-71.405811,41.764486;-71.445465,41.786057;-71.446238,41.801095;-71.489410,41.817281;-71.440487,41.866467;-71.440487,41.866467';
    $content = file_get_contents($url);
    if ($content === false)
    {
        echo("ERROR: Could not connect to Waze GeoJSON.");
    }
    // echo $content;
    // $json = json_decode($content, TRUE); // TRUE returns JSON as a PHP array

    $json = json_decode($content); // without TRUE, returns JSON as PHP object

    // print(json_encode($json, JSON_PRETTY_PRINT));
    // var_dump($json);

    foreach($json->alerts as $item)
    {
        // var_dump($item);
        if ($item->subtype === "HAZARD_ON_ROAD_POT_HOLE")
        {
            $count++;
            // echo("Got one!\n");

//          if (!exists($item->roadType))
//          {
//              $item->roadType = -1;
//          }
//          if (!exists($item->city))
//          {
//              $item->city = "***UNDEFINED***";
//          }
            // put it in the database
            $fields = "uuid, pubMillis, timesUpdated, longitude, latitude, roadType, street, country, city, type, subtype, magvar, reportDescription, reportRating, confidence, reliability";
            $result = CS50::query("INSERT IGNORE INTO `markers` ($fields) VALUES(?, ?, 1, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE timesUpdated = timesUpdated + 1", $item->uuid, $item->pubMillis, $item->location->x, $item->location->y, $item->roadType, $item->street, $item->country, $item->city, $item->type, $item->subtype, $item->magvar, $item->reportDescription, $item->reportRating, $item->confidence, $item->reliability);
        }
    }
    if ($count === 0)
    {
        echo "No potholes found.\n";
    }
    else if ($count === 1)
    {
        echo "1 pothole found.\n";
    }
    else
    {
        echo $count . " potholes found.\n";
    }
?>
