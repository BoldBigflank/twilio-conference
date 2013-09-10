<?php
$UID=$_GET['uid'];
// echo $UID;

// Connect to database
require_once('config.php');
$link = mysqli_connect("$mysql_server", "$mysql_user", "$mysql_pw", "$mysql_db");
if (!$link) {
    die('Could not connect: ' . mysql_error());
}

// Update the database
if($_POST){
	$greeting = $_POST["greeting"];
	$record_names = isset($_POST["recordNames"]) ? 1 : 0;
	$record_call = isset($_POST["recordCall"]) ? 1 : 0;
	$sql = "UPDATE users SET greeting = '$greeting', record_names = $record_names, record_call = $record_call WHERE user_ID = '$UID' ;";
	mysqli_query ($link, $sql);
}


// Get conference data
if($result = mysqli_query($link, "SELECT * FROM users WHERE user_ID = '$UID' LIMIT 1") ){
		if($data = mysqli_fetch_assoc($result)){
			$PIN = $data["user_pin"];

			$greeting = $data["greeting"];
			$record_names = $data["record_names"];
			$record_call = $data["record_call"];
			date_default_timezone_set('UTC');
			$now = new DateTime();

			// Get participant count for each conference, put into an object
			$participants_count = array();
			
			$sql = "SELECT conference_sid, count(*) as count FROM participants WHERE pin='$PIN' GROUP BY conference_sid";
			$sql_participants = mysqli_query($link, $sql);
			while ($sql_participant = mysqli_fetch_assoc($sql_participants)){
				$participants_count[$sql_participant['conference_sid']] = $sql_participant['count'];
			}
			mysqli_free_result($results);
			var_dump($participants_count);
			
			$call_history = "";
			// Loop over the list of conferences and echo a property for each one
			foreach ($client->account->conferences->getIterator(0, 50, array(
			        "FriendlyName" => $PIN
			    )) as $conference
			) {
				$count = isset($participants_count["$conference->sid"]) ? $participants_count["$conference->sid"] : 0;
				$startTime = new DateTime($conference->date_created);
				if($conference->status == "in-progress") $endTime = new DateTime();
				else $endTime = new DateTime($conference->date_updated);

				$duration = date_diff($endTime, $startTime);
				$durationText = $duration->format( "%h:%I:%S" );

				$participant_recordings = "";
				$recordings_sql = "SELECT call_recording FROM participants WHERE conference_sid='$conference->sid' AND call_recording IS NOT NULL";
				$recordings_result = mysqli_query($link, $recordings_sql);
				while($recording = mysqli_fetch_assoc($recordings_result)){
					$uri = $recording['call_recording'];
					$participant_recordings .= "<audio controls='controls' style=''><source id='greeting_source' src='$uri'></audio><br>";
				}
				
				// Date Status Duration Participants Recording Transcript
				$call_history .= "<tr><td>$conference->date_created</td><td>$conference->status</td><td>$durationText</td><td>$count</td><td>$participant_recordings</td><td>$conference->uri</td></tr>";
			}

			$participants = "";
			foreach ($client->account->conferences->getIterator(0, 50, array(
					"Status" => 'in-progress',
			        "FriendlyName" => $PIN
			    )) as $conference
			) {
				foreach($conference->participants as $participant){
					$call = $client->account->calls->get($participant->call_sid);
					$recordings = "";
					foreach($call->recordings as $recording){
						// Get the recording again, to avoid issues
						$recording = $client->account->recordings->get($recording->sid);

						$uri = "https://api.twilio.com/2010-04-01/Accounts/$sid/Recordings/" . $recording->sid . ".mp3";

						if($recording->duration > 0){
							$name = "";
							
							if(isset($recording->transcriptions) ){
								foreach($recording->transcriptions as $transcription){
									var_dump($transcription);
									$name = $transcription->transcription_text . "<br>";
								}
							}
							$recordings .= "<audio controls='controls' style=''><source id='greeting_source' src='$uri'></audio><br>";
						}
					};
					// Date number name recording
					$participants .= "<tr><td>$participant->date_created</td><td>$call->from</td><td>$name</td><td>$recordings</td></tr>";
				}
			}


		} else {
			echo "Data not found";
			// Not a valid uid
		}
		// mysqli_free_result($result);
	}
	else {
		echo "Query not done";
	}
// If there is POST information, update the database
// if($_POST) echo var_dump($_POST);


// Get the information for the form



// Get Twilio information on each conference call, participants of the curent one


?><!DOCTYPE html>
<html>
  <head>
    <title>Conference Detail</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap -->
    <link href="<?php echo $host; ?>/css/bootstrap.min.css" rel="stylesheet" media="screen">
  </head>
  <body>
  	<div class="container">
  		<div class="well well-large">
		    <h1>Conference Details (PIN: <?php echo $PIN; ?> )</h1>
		    <form class="form-horizontal" action="#" method="post">
		      <div class="control-group">
		        <label class="control-label" for="welcomeMessage">Welcome Message</label>
		        <div class="controls">
		          <input type="text" id="welcomeMessage" name="greeting" placeholder="Welcome" value="<?php echo $greeting; ?>">
		        </div>
		      </div>
		      <div class="control-group">
		        <div class="controls">
		          <label class="checkbox">
		            <input type="checkbox" id="recordNames" name="recordNames" <?php if($record_names == 1) echo 'checked'; ?> > Record names and announce entry
		          </label>
		          <label class="checkbox">
		            <input type="checkbox" id="recordCall" name="recordCall" <?php if($record_call == 1) echo 'checked'; ?> > Record the call
		          </label>
		          <button type="submit" class="btn">Save</button>
		        </div>
		      </div>
		    </form>
		</div>
	    <h1>Current Status</h1>
		<table class="table table-bordered table-striped">
	      <thead>
	      	<tr>
	      		<th>Date</th>
	      		<th>Participant Number</th>
	      		<th>Participant Name</th>
	      		<th>Participant Recording</th>
	      	</tr>
	      </thead>
	      <tbody>
	      	<?php // For each participant in the current call, display a row
	      		echo $participants;
	      	 ?>
	      </tbody>
	    </table>
	    <h1>Call History</h1>
	    <table class="table table-bordered table-striped">
	      <thead>
	      	<tr>
	      		<th>Date</th>
	      		<th>Status</th>
	      		<th>Duration</th>
	      		<th>Number of Participants</th>
	      		<th>Recording</th>
	      		<th>Transcript</th>
	      	</tr>
	      </thead>
	      <tbody>
	      	<?php // For each conference call found in the log, display a row
	      		echo $call_history;
	      	 ?>
	      </tbody>
	    </table>


	    <script src="http://code.jquery.com/jquery.js"></script>
	    <script src="<?php echo $host; ?>/js/bootstrap.min.js"></script>
	</div>
  </body>
</html>