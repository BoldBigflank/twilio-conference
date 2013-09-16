<?php

require_once('./config.php'); // Configuration variables
$link = mysqli_connect("$mysql_server", "$mysql_user", "$mysql_pw", "$mysql_db");
if (!$link) {
    die('Could not connect: ' . mysql_error());
}

$NameUrl = (isset($_REQUEST['NameUrl']) ) ? $_REQUEST['NameUrl'] : "''";
$PIN = (isset($_REQUEST['PIN']) ) ? $_REQUEST['PIN'] : "";
$RecordingUrl = (isset($_REQUEST['RecordingUrl']) ) ? $_REQUEST['RecordingUrl'] : NULL;
$CallSid = (isset($_REQUEST['CallSid']) ) ? $_REQUEST['CallSid'] : "";

date_default_timezone_set('UTC');

// Get the most recent conference
$most_recent_conference = NULL;
foreach (
    $client->account->conferences->getIterator(
        0, 50, array(
            "FriendlyName" => $PIN,
            "DateUpdated>" => date("Y-m-d")
        )
    ) as $conference
) {
    if($most_recent_conference == NULL){
        $most_recent_conference = $conference;
        continue;
    }
    $date1 = new DateTime($most_recent_conference->DateUpdated);
    $date2 = new DateTime($conference->DateUpdated);
    if( $date1 < $date2 ) $most_recent_conference = $conference;
}

// Update the participants table
$recording_sql = ($RecordingUrl != NULL) ? ", call_recording = '$RecordingUrl'" :"";
$sql = "UPDATE participants SET conference_sid = '$most_recent_conference->sid' $recording_sql WHERE pin='$PIN' AND call_sid='$CallSid'";
mysqli_query($link, $sql);

// If there are other participants, Initiate a call for the announcer
if( $NameUrl != "''" && $most_recent_conference->status == "in-progress" && count( $most_recent_conference->participants ) > 0 ){
    $To = $_REQUEST['To'];

    $call = $client->account->calls->create($To, $To, "$host/announce-end.php?RecordingUrl=" . urlencode($NameUrl), array(
        "SendDigits" => "$PIN"
        ));
}


?>