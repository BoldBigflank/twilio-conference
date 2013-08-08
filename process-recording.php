<?php

require_once('./config.php'); // Configuration variables

if(isset($_REQUEST['RecordingUrl']) ) $RecordingUrl = $_REQUEST['RecordingUrl'];
else $RecordingUrl="";

$PIN = $_GET['PIN'];

$response = "<Response>\n";

if( !isset( $_REQUEST['RecordingUrl'] ) ){
    $response .= "<Say language='en-gb'>Record your name now</Say>";
    $response .= "<Record action='./process-recording.php?PIN=$PIN' maxLength='5' timeout='2' />";
    $response .= "<Redirect>./process-recording.php?PIN=$PIN&amp;RecordingUrl=''</Redirect>";
} else {
	// Connect the user to the room
	$PIN = $_GET['PIN'];
	$response .= "<Say language='en-gb'>Now entering.</Say>";
	$response .= "<Dial action='$host/call-end.php?PIN=$PIN&amp;RecordingUrl=" . urlencode($RecordingUrl) . "'><Conference beep='false' waitUrl=''>$PIN</Conference></Dial>";

    // Initiate a call for the announcer
    $To = $_REQUEST['To'];

    // Choose whether to announce that they are the first or play the recording to others
    $announceUrl = "$host/announce-first.php"; // The default announcement
    foreach (
        $client->account->conferences->getIterator(
            0, 1, array(
                "Status" => "in-progress",
       	        "FriendlyName" => $PIN
            )
        ) as $conference
    ) {
        if( count( $conference->participants ) > 0 )
            $announceUrl = "$host/announce.php?RecordingUrl=" . urlencode($RecordingUrl); // The updated announcement
    }

    // Start the announcement call
    $call = $client->account->calls->create($To, $To, $announceUrl, array(
        "SendDigits" => "$PIN"
        ));

}

$response .="</Response>";
print $response;

?>