<?php
// Announce the entry of the new person

$RecordingUrl = isset($_GET['RecordingUrl']) ? $_GET['RecordingUrl'] : "''";

$name = ($RecordingUrl != "" ) ? "<Play>$RecordingUrl</Play>" : "<Say voice='alice' language='en-GB'>A user</Say>";

?>
<?xml version="1.0" encoding="UTF-8"?>
<Response>
	<?=$name?>
	<Say voice="alice" language="en-GB">has joined the conference.</Say>
	<Hangup />
</Response>