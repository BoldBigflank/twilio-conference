<?php

require_once('./config.php'); // Configuration variables

if(isset($_REQUEST['RecordingUrl']) ) $RecordingUrl = $_REQUEST['RecordingUrl'];
else $RecordingUrl="";
if(isset($_REQUEST['PIN']) ) $PIN = $_REQUEST['PIN'];
else $PIN="";

// Initiate a call for the announcer
$To = $_REQUEST['To'];

$call = $client->account->calls->create($To, $To, "$host/announce-end.php?RecordingUrl=" . urlencode($RecordingUrl), array(
    "SendDigits" => "$PIN"
    ));



?>