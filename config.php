<?php

$host = ""; // Path to files with no trailing '/', ie. "http://dev.bold-it.com/conference"

$mysql_server = "127.0.0.1";
$mysql_user = "root";
$mysql_pw = "";
$mysql_db = "conference_db";

// Twilio Helper Library
require_once('./twilio-php/Services/Twilio.php'); // Loads the library

$sid = ""; // TWILIO AccountSid
$token = ""; // TWILIO AuthToken
$client = new Services_Twilio($sid, $token);

?>