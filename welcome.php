<?php

?>
<?xml version="1.0" encoding="UTF-8"?>
<Response>
	<Gather action = './process-pin.php' method='GET' numDigits='6' timeout='8'>
		<Say language="en-gb">Please enter your 6-digit pin</Say>
	</Gather>
	<Redirect>#</Redirect>
</Response>