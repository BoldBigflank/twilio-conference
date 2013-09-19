<?php
// Announce the leaving of the person

$RecordingUrl = isset($_GET['RecordingUrl']) ? $_GET['RecordingUrl'] : "''";

$name = ($RecordingUrl != "" ) ? "<Play>$RecordingUrl</Play>" : "<Say voice='alice' language='en-GB'>A user</Say>";

?>
<?xml version="1.0" encoding="UTF-8"?>
<Response>
	<?=$name?>
	<Say voice="alice" language="en-GB">has left the conference.</Say>
	<Hangup />
</Response>