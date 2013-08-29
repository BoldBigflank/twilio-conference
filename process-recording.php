<?php

require_once('./config.php'); // Configuration variables
$link = mysqli_connect("$mysql_server", "$mysql_user", "$mysql_pw", "$mysql_db");
if (!$link) {
    die('Could not connect: ' . mysql_error());
}

if(isset($_REQUEST['RecordingUrl']) ) $RecordingUrl = $_REQUEST['RecordingUrl'];
else $RecordingUrl=false;

$PIN = $_GET['PIN'];

// Check whether we should record the user's name in the database
if($result = mysqli_query($link, "SELECT * FROM users WHERE user_pin = '$PIN' LIMIT 1") ){
    if($data = mysqli_fetch_assoc($result)){
        $PIN = $data["user_pin"];

        $greeting = $data["greeting"];
        $record_names = ($data["record_names"] == 1) ? true : false; // This is for us to process
        $record_call = ($data["record_call"]) ? "true" : "false"; // This is passed in the TwiML

    }
};


$response = "<Response>\n";
// If we record the names and don't have a recording url, ask for one
if( $record_names && !isset( $_REQUEST['RecordingUrl'] ) ){
    if( !isset($greeting) ) $greeting = "";
    $response .= "<Say language='en-gb'>$greeting.  Record your name now.</Say>";
    $response .= "<Record action='./process-recording.php?PIN=$PIN' maxLength='5' timeout='2' />";
    $response .= "<Redirect>./process-recording.php?PIN=$PIN&amp;RecordingUrl=''</Redirect>";
} else {
	$PIN = $_GET['PIN'];
	
    // Initiate a call for the announcer
    $To = $_REQUEST['To'];

    // Choose whether to announce that they are the first or play the recording to others
    $announceUrl = false; // The default announcement
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

    // If we have a recording and there are others, announce to them
    if($RecordingUrl && $announceUrl !== false){
        $call = $client->account->calls->create($To, $To, $announceUrl, array(
            "SendDigits" => "$PIN"
        ));
    }

    // Connect the user to the room
    $response .= "<Say language='en-gb'>Now entering.</Say>";
    if(!$announceUrl) $response .= "<Say voice='alice' language='en-GB'>You are the first person in the conference.</Say>";
    $response .= "<Dial action='$host/call-end.php?PIN=$PIN&amp;NameUrl=" . urlencode($RecordingUrl) . "' record='$record_call'><Conference beep='false' waitUrl=''>$PIN</Conference></Dial>";


}

$response .="</Response>";
print $response;

?>