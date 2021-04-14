<?php

// Params to connect to a db
$dbHost = "localhost";
$dbUser = "root";
$dbPass = "";
$dbName = "";

$clientId = "[insert imgur api token]";

$site_domain = "http://localhost";
$site_path = "/shobi";
$site_url = $site_domain . $site_path;

$conn = mysqli_connect($dbHost, $dbUser, $dbPass, $dbName);

if ($conn) {

} else {
    die("ERROR: Could not connect. " . mysqli_connect_error());
}

?>