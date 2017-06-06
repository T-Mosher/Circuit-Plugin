<?php
function nwapaGetUnitInfo(string $id) {
	echo '<br>Testing the function<br>';

}

function nwapaGetActiveEvents() {
	// this function returns an array of all active events in chronological order
	global $wpdb;
	
	$events = array();
	$query = "SELECT * FROM events WHERE event_active = 'T' ORDER BY event_date ASC";
	$response = $wpdb->get_results($query, ARRAY_A);
	if(!empty($response)) {
		foreach($response as $row) {
			// push this event onto the array
			$events[] = $row;
		}
	} else {
		echo '<br>No active events found.<br>';
		$wpdb->print_error();
	}
	return $events;
}

function nwapaGetEntryCounts() {
	// this function returns an assoc array giving the number of entries in each active event
	// for each row in the return array:
	//    'event_id' is the event number
	//    'cnt' is the number of entries in that event
	global $wpdb;
	
	$events = nwapaGetActiveEvents();
	$entries = array();
	foreach ($events as $e) {
		// Debug
		//echo '<br>Testing event:'.$e['event_id'].' Name: '.$e['event_name'].'<br>';
		
		$query = "SELECT * FROM entries WHERE what_event = ".$e['event_id'];
		$entries_resp = $wpdb->get_results($query, ARRAY_A);
		$cnt = 0;
		if (!empty($entries_resp)) {
			
			foreach($entries_resp as $entry) {
				if ($e['event_id'] ===  $entry['what_event']) {
					$cnt = $cnt + 1;
				}
			}
			// Debug
			//echo '<br>Entries for this event: '.$cnt.'<br>';
		} else {
			// Debug
			//echo '<br>No entries in this event.<br>';
		}
		$entries[$e['event_id']] = $cnt;
	}
	// Debug
	//var_disp($entries);
	return $entries;
}

function var_disp($var) {
	// this function puts some HTML framing around var_dump()'s output
	echo '<br>';
	var_dump($var);
	echo '<br>';
}

// Note: need to update this function name
function get_nwapa_settings($param) {
/*
* Read a parameter $param from the nwapa_settings table
*/
  global $wpdb;
  $val = FALSE;
  $msg = NULL;
  $query = "SELECT * FROM nwapa_settings";
  $response = $wpdb->get_results($query, ARRAY_A);
  // Debug
  //var_disp($response);
  if (!empty($response)){
	  foreach($response as $r) {
		  if (strcmp($r['parameter'], $param) == 0) {
			  $val = $r['value'];
			  $msg = $r['message'];
		} 
	  }
  } else {
	  die('<br>No nwapa settings table found<br>');
  }
  return array('val'=>$val,'msg'=>$msg);
}


?>