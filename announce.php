<?php
// Announce the entry of the new person

$RecordingUrl = $_GET['RecordingUrl'];

?>
<?xml version="1.0" encoding="UTF-8"?>
<Response>
	<Say voice="woman">Now entering</Say>
	<Play><?=$RecordingUrl?></Play>
	<Hangup />
</Response>