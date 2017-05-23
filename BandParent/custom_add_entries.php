<?php
/*
Template Name: Custom Add Entries
*/
?>

<?php
global $wpdb;

get_header();

echo 'This is the custom template for adding records to the events table.<br><br>';
// method is borrowed from "studentadded.php" tutorial

echo 'Do I have the participant id?'.$_REQUEST['pid'].'<br>';

echo 'How big is _Request? '.count($_REQUEST).'<br>';
foreach($_REQUEST as $rq) {
	print_r($rq);
	echo '<br>';
}

echo 'How big is _Post? '.count($_POST).'<br>';

echo 'check all of the eids<br>';
var_dump($_POST['eids']);
echo '<br>';

$part_id = Participants_Db::get_participant_id($_REQUEST['pid']);
echo "part_id:".$part_id."<br>";

// get all of the fields of this record into an indexable array
$part_id_fields = Participants_Db::get_participant($part_id);
echo '========= Updating event entries for:'.$part_id_fields['unit_name'].'========<br>';

var_dump($part_id_fields);
echo '<br>';

if (isset($_POST['submit'])){
	echo 'got a submit request<br>';
	echo $_POST['submit'].'<br>';
}

// get a list of all events this unit is entered in
$query = "SELECT * FROM entries WHERE what_participant = ".$part_id;
$response = $wpdb->get_results($query, ARRAY_A);
// event list starts as an empty array
$my_event_list = array();
echo 'list of my current event entries<br>';
if (!empty($response)) {
	foreach($response as $row) {	
		echo 'Event:'.$row['what_event'].' Participant:'.$row['what_participant'].'<br>';
		// built an array with this unit's events by pushing new elements
		$my_event_list[] = $row['what_event'];
	}	
} else {
	echo 'Sorry, no event entries found for this unit.<br>';
}
echo 'Show the list of events for this unit<br>';
var_dump($my_event_list);
echo '<br>';

//$query = "SELECT event_id,event_date,event_name,event_host,event_location,event_capacity,event_type FROM events";
//$query = "SELECT * FROM entries WHERE what_participant = ";
$query = "SELECT event_id FROM events WHERE event_active = 'T'";
// note: can speed the later processing by excluding events of the wrong type for this unit
$response = $wpdb->get_results($query, ARRAY_A);

if ($response) {
	echo 'got a response for "events" table<br>';
	//print_r($response);
	foreach($response as $rsp) {
		echo 'event id:'.$rsp['event_id'].'<br>';
		$entered_this_event = array_search($rsp['event_id'], $my_event_list);
		$checked_this_event = array_search($rsp['event_id'], $_POST['eids']);
		
		// debugging
		//var_dump($found);
		
		// instead of testing for $found == FALSE, try checking if it is an integer or a BOOL
		if (is_int($entered_this_event)) {
			echo "found this event<br>";
			// already entered in this event. So if it isn't checked on the form, then delete then entry
			if (!is_int($checked_this_event)) {
				echo "delete this entry xxxxx<br>";
				$rslt = $wpdb->delete('entries',
					array(
						'what_event' => $rsp['event_id'],
						'what_participant' => $part_id
					));
				// DEBUG
				var_dump($rslt);
				echo '<cr>';
				// error check the $rslt value?
			} else {
				echo "already entered - do nothing<br>";
			}
			
		} else {
			echo "didn't find this event<br>";
			// i'm not entered in this event. If it is checked on the form, then add an entry
			if (is_int($checked_this_event)) {
				echo "add an entry for this event +++++<br>";
				//$query = "INSERT INTO entries (what_event what_participant entry_type)
				$rslt = $wpdb->insert('entries',
					array(
						'what_event' => $rsp['event_id'],
						'what_participant' => $part_id,
						'entry_type' => 'COMPETING'
						));
				$idd = $wpdb->insert_id;
				// DEBUG
				var_dump($rslt);
				echo '<br>';
				// error-check the result code
				if ($rslt != 1) {
					echo '####### Failure adding entry to event<br>';
				} else {
					echo 'entries added. New entry id is '.$idd.'<br>';
				}
			} else {
				echo "not entered - do nothing<br>";
			}
		}
			
		
		
		
	}
} else {
	echo "Found no active events for this unit type.<br>";
}
	

echo '<br><br>';
// display the footer
get_footer();
?>

