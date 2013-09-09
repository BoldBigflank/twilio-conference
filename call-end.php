<?php

require_once('./config.php'); // Configuration variables
$link = mysqli_connect("$mysql_server", "$mysql_user", "$mysql_pw", "$mysql_db");
if (!$link) {
    die('Could not connect: ' . mysql_error());
}

if(isset($_REQUEST['NameUrl']) ) $RecordingUrl = $_REQUEST['NameUrl'];
else $RecordingUrl="";
if(isset($_REQUEST['PIN']) ) $PIN = $_REQUEST['PIN'];
else $PIN="";



// Get the most recent conference
foreach (
    $client->account->conferences->getIterator(
        0, 1, array(
            "FriendlyName" => $PIN
        )
    ) as $conference
) {
    // Update the participants table
    $sql = "UPDATE participants SET conference_sid = '$conference->sid' WHERE pin='$PIN' AND conference_sid IS NULL";
    mysqli_query($link, $sql);

    // If there are other participants, Initiate a call for the announcer
    if( $conference->status == "in-progress" && count( $conference->participants ) > 0 ){
    	$To = $_REQUEST['To'];

    	$call = $client->account->calls->create($To, $To, "$host/announce-end.php?RecordingUrl=" . urlencode($RecordingUrl), array(
    	    "SendDigits" => "$PIN"
    	    ));
    }
}


?>