<?php

require_once('./config.php'); // Configuration variables

if(isset($_REQUEST['NameUrl']) ) $RecordingUrl = $_REQUEST['NameUrl'];
else $RecordingUrl="";
if(isset($_REQUEST['PIN']) ) $PIN = $_REQUEST['PIN'];
else $PIN="";



// If there are other participants, Initiate a call for the announcer
foreach (
    $client->account->conferences->getIterator(
        0, 1, array(
            "Status" => "in-progress",
   	        "FriendlyName" => $PIN
        )
    ) as $conference
) {
    if( count( $conference->participants ) > 0 ){
    	$To = $_REQUEST['To'];

    	$call = $client->account->calls->create($To, $To, "$host/announce-end.php?RecordingUrl=" . urlencode($RecordingUrl), array(
    	    "SendDigits" => "$PIN"
    	    ));
    }
}


?>