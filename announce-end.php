<?php
// Announce the leaving of the person

$RecordingUrl = $_GET['RecordingUrl'];

?>
<?xml version="1.0" encoding="UTF-8"?>
<Response>
	<Play><?=$RecordingUrl?></Play>
	<Say voice="alice" language="en-GB">has left the conference.</Say>
	<Hangup />
</Response>