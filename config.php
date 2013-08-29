<?php

$host = "http://dev.bold-it.com/conference";

$mysql_server = "127.0.0.1";
$mysql_user = "root";
$mysql_pw = "";
$mysql_db = "conference_db";

// Twilio Helper Library
require_once('./twilio-php/Services/Twilio.php'); // Loads the library

$sid = "ACb26e0a7db2f04327b1133eab63012b0b"; 
$token = "778f875178eea976246ae5d5b42bc4c9"; 
$client = new Services_Twilio($sid, $token);

?>