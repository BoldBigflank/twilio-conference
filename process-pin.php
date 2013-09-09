<?php
require_once('./config.php');
// Connect the database
$link = mysqli_connect("$mysql_server", "$mysql_user", "$mysql_pw", "$mysql_db");
if (!$link) {
    die('Could not connect: ' . mysql_error());
}

// Retrieve the user input
if(isset($_GET['Digits']) ) $PIN = $_GET['Digits'];
else $PIN = "";
if(isset($_GET['RecordingUrl']) ) $RecordingUrl = $_GET['RecordingUrl'];

// Prepare the user response
$response = "<Response>\n";
$validPIN = false;

// If we have a PIN, compare it to database
if(strlen($PIN)==6){
	//Connect to database
	
	// Find the PIN in the database
	if($result = mysqli_query($link, "SELECT * FROM users WHERE user_pin = '$PIN' LIMIT 1") ){
		$validPIN = mysqli_num_rows($result) > 0;
		mysqli_free_result($result);
	};

	if( $validPIN ){
		// Announcers do not record their name
		if($_REQUEST['To']==$_REQUEST['From']){
			$response .= "<Dial record='false'><Conference beep='true'>$PIN</Conference></Dial>";
		} else {
			$response .= "<Say language='en-gb'>PIN accepted.</Say>";
			$response .= "<Redirect>./process-recording.php?PIN=$PIN</Redirect>";
		}
		// Join the room
	} else {
		// incorrect PIN
		$response .= "<Say language='en-gb'>You have entered an invalid pin, please try again.</Say>";
		$response .= "<Redirect>./welcome.php</Redirect>";
	}
} else {
	$response .= "<Say language='en-gb'>You have entered an invalid pin, please try again.</Say>";
	$response .= "<Redirect>./welcome.php</Redirect>";
}
$response .= "</Response>";
print $response;

?>