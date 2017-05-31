<?php
/*
Template Name: Custom Add Entries
*/
?>

<?php
global $wpdb;

get_header();

//echo 'Info: custom_add_entries template - adds and updates records in the events table.<br><br>';
//method is borrowed from "studentadded.php" tutorial

// DEBUG
// need some error checking...
//echo 'Do I have the participant id?'.$_REQUEST['pid'].'<br>';

//echo 'How big is _Request? '.count($_REQUEST).'<br>';
//foreach($_REQUEST as $rq) {
//	print_r($rq);
//	echo '<br>';
//}

//echo 'How big is _Post? '.count($_POST).'<br>';

//echo 'display all of the eids<br>';
//var_dump($_POST['eids']);
//echo '<br>';

$part_id = Participants_Db::get_participant_id($_REQUEST['pid']);
//echo "part_id:".$part_id."<br>";

// get all of the fields of this record into an indexable array
$part_id_fields = Participants_Db::get_participant($part_id);
echo '<h3>========= Updating event entries for:'.$part_id_fields['unit_name'].' ========</h3>';

// DEBUG
//var_dump($part_id_fields);
//echo '<br>';

//if (isset($_POST['submit'])){
//	echo 'got a submit request<br>';
//	echo $_POST['submit'].'<br>';
	$sbmt = $_POST['submit'];
	//var_dump($sbmt);
	//echo "<br>";
	if ($sbmt != 'Save Events') {
		// die if the submit button title was not correct
		die('Received invalid submit request:'.$sbmt);
	}
//}

// get a list of all events this unit is entered in
$query = "SELECT * FROM entries WHERE what_participant = ".$part_id;
$entry_response = $wpdb->get_results($query, ARRAY_A);

// get list of active events - OK to find all event types, as only one season is active at a time
// but we could be more fancy if we wanted to...
//$query = "SELECT * FROM events WHERE find_in_set('".$unit_type_uc."', event_type) > 0 AND event_active = 'T' ORDER BY event_date";
$query = "SELECT event_id, event_name, event_location FROM events WHERE event_active = 'T' ORDER BY event_date";
$event_response = $wpdb->get_results($query, ARRAY_A);
$active_event_array = array();
	//print_r($response);
	if (!empty($event_response)) {
		foreach($event_response as $row) {	
			//echo 'Event:'.$row['what_event'].' Participant:'.$row['what_participant'].'<br>';
			// built an array of active events by pushing new elements
			$active_event_array[] = $row['event_id'];
		}	
	} else {
		// there were no active events
		// this shouldn't happen, so die
		die('No active events were found.');
	}
// DEBUG
//echo "var dump of active_event_array<br>";
//var_dump($active_event_array);
//echo "<br>";

// event list starts as an empty array
$my_event_list = array();
//echo 'list of my current entries in active events...<br>';
if (!empty($entry_response)) {
	foreach($entry_response as $row) {	
		//var_dump($row['what_event']);
		//echo "<br>";
		if (in_array($row['what_event'], $active_event_array)) {
			//echo 'Event:'.$row['what_event'].' Participant:'.$row['what_participant'].'<br>';
			// built an array with this unit's events by pushing new elements
			$my_event_list[] = $row['what_event'];
		} else {
			// all entries in previous events pass through here
			//echo "Warning: Event ".$row['what_event']." is not active.<br>";
		}
	}	
} else {
	echo 'No event entries found for this unit.<br>';
}

// DEBUG
//echo 'Show the list of active events for this unit<br>';
//var_dump($my_event_list);
//echo '<br>';

// note: already did this query previously
//$query = "SELECT event_id FROM events WHERE event_active = 'T'";
//$response = $wpdb->get_results($query, ARRAY_A);

if ($event_response) {
	//echo 'got a response for "events" table<br>';
	//print_r($response);
	
	// create a results table
	echo '<h4>Unit activity record</h4>';
	echo '<table>';
	echo '<tr><th>Activity</th><th>Event</th><th>Location</th></tr>';

	foreach($event_response as $rsp) {
		//echo 'event id:'.$rsp['event_id'].'<br>';
		$entered_this_event = array_search($rsp['event_id'], $my_event_list);
		if (empty($_POST['eids'])) {
			//echo 'eids is empty<br>';
			$checked_this_event = FALSE;
		} else {
			$checked_this_event = array_search($rsp['event_id'], $_POST['eids']);
		}
		
		// debugging
		//var_dump($found);
		
		// instead of testing for $found == FALSE, try checking if it is an integer or a BOOL
		if (is_int($entered_this_event)) {
			//echo "found this event<br>";
			// already entered in this event. So if it isn't checked on the form, then delete then entry
			if (!is_int($checked_this_event)) {
				//echo "delete this entry<br>";
				$rslt = $wpdb->delete('entries',
					array(
						'what_event' => $rsp['event_id'],
						'what_participant' => $part_id
					));
				// DEBUG
				//echo "delete entry result<br>";
				//var_dump($rslt);
				//echo '<br>';
				// error check the result code
				if ($rslt != 1) {
					echo 'Log Warning: ####### Unexpected delete result:'.$rslt.'<br>';
					$fld1 = 'Err';
				} else {
					//echo 'one entry deleted<br>';
					$fld1 = 'Delete';
				}
			} else {
				//echo "already entered - keep and do nothing<br>";
				$fld1 = 'Keep';
			}
			
		} else {
			//echo "didn't find this event<br>";
			// i'm not entered in this event. If it is checked on the form, then add an entry
			if (is_int($checked_this_event)) {
				//echo "add an entry for this event +++++<br>";
				//$query = "INSERT INTO entries (what_event what_participant entry_type)
				$rslt = $wpdb->insert('entries',
					array(
						'what_event' => $rsp['event_id'],
						'what_participant' => $part_id,
						'entry_type' => 'COMPETING'
						));
				$idd = $wpdb->insert_id;
				// DEBUG
				//var_dump($rslt);
				//echo '<br>';
				
				// error-check the result code
				if ($rslt != 1) {
					echo 'Log Warning: ####### Failure adding entry to event<br>';
					$fld1 = 'Err:Add';
				} else {
					//echo 'entries added. New entry id is '.$idd.'<br>';
					$fld1 = 'Add';
				}
			} else {
				//echo "not entered - do nothing<br>";
				$fld1 = NULL; 
			}
		}
			
		// output the row to the table
		if (!empty($fld1)){
			echo '<tr><td>'.$fld1.'</td><td>'.$rsp['event_name'].'</td><td>'.$rsp['event_location'].'</td></tr>';
		}
		
	}
	// end the activity summary table
	echo '</table>';
	echo 'Note: if the activity record is blank, it means the unit has no entries in active events.<br>';
	echo 'User your browser "Back" button to return to the unit information page<br>';
} else {
	echo "Found no active events for this unit type.<br>";
}

echo '<br><br>';
// display the footer
get_footer();
?>

