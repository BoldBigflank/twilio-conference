<?php

require_once('./config.php'); // Configuration variables

if(isset($_REQUEST['RecordingUrl']) ) $RecordingUrl = $_REQUEST['RecordingUrl'];
else $RecordingUrl="";

$PIN = $_GET['PIN'];

$response = "<Response>\n";

if($RecordingUrl == ""){
    $response .= "<Say>Record your name now</Say>";
    $response .= "<Record action='./process-recording.php?PIN=$PIN' maxLength='5' timeout='2' />";
    $response .= "<Redirect>./welcome.php</Redirect>";
} else {
	// Connect the user to the room
	$PIN = $_GET['PIN'];
	$response .= "<Say>Now entering.</Say>";
	$response .= "<Dial><Conference beep='false'>$PIN</Conference></Dial>";

    // Initiate a call for the announcer
    $To = $_REQUEST['To'];

    $call = $client->account->calls->create($To, $To, "$host/announce.php?RecordingUrl=" . urlencode($RecordingUrl), array(
        "SendDigits" => "w$PIN"
        ));
    
	// // Get the list of participants, announce the new entry
	// foreach ($client->account->conferences->getIterator(0, 50, array(
 //        "Status" => "in-progress",
 //    	"FriendlyName" => $PIN
 //    )) as $conference ) {
 //    	// Redirect each participant to hear the entering message
 //    	foreach ($conference->participants as $participant) {
 //    		// 
 //    	    $call = $client->account->calls->get($participant->call_sid);
 //    	    $call->update(array(
 //    	    	"Url"=>"/entry.php?RecordingUrl=" . urlencode($RecordingUrl) . "&PIN=$PIN"
 //    	    ));

 //    	}
	// }
}

$response .="</Response>";
print $response;

?>