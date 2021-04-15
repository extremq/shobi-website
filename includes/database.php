<?php

$env = parse_ini_file('./.env');

// Params to connect to a db
$dbHost = $env['dbHost'];
$dbUser = $env['dbUser'];
$dbPass = $env['dbPass'];
$dbName = $env['dbName'];

$pagination = 5;

$clientId = $env['clientId'];

$site_domain = "http://localhost";
$site_path = "/shobi";
$site_url = $site_domain . $site_path;

$conn = mysqli_connect($dbHost, $dbUser, $dbPass, $dbName);

if ($conn) {

} else {
    die("ERROR: Could not connect. " . mysqli_connect_error());
}

?>