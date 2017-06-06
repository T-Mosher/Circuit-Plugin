<?php
/*
Template Name: Custom Events
*/
?>

<?php
global $wpdb;

	get_header();

	echo '<br>';
	echo '<h3>This page displays all scheduled events.</h3>';
	echo '<p>To enter an event, use your unit registration link.</p>';
	echo '<p>To view the event and entry list, click on the Event Name link.</p>';
	echo '<br>';

	$events = nwapaGetActiveEvents();

	echo '<table style=bold cellpadding=3>
		<tr>
		<td><b>Date</b></td>
		<td><b>Event Name</b></td>
		<td><b>Location</b></td>
		<td><b>Event Type</b></td>
		<td><b>Entries</b></td>
		<td><b>Capacity</b></td>
		</tr>';
	
	// get the count of entries in each event	
	$event_entries = nwapaGetEntryCounts();
	// Debug
	//var_disp($event_entries);
	
	foreach($events as $row) {
		// populate each row of the event summary table
		echo '<tr>';
		//echo '<td>' . $row['event_date'] .' id:'.$row['event_id'] . '</td>';
		echo '<td>' . $row['event_date'] . '</td>';
		echo '<td>' . '<a href="'.$row['event_link'].'">'.$row['event_name'] . '</a></td>';
		echo '<td>' . $row['event_location'] . '</td>';
		echo '<td>' . $row['event_type']. '</td>';
		//echo '<td>--</td>';
		echo '<td>' . $event_entries[$row['event_id']].'</td>';
		echo '<td>' . $row['event_capacity'] . '</td>';
 		echo '</tr>';
	}
	echo '</table>';
	echo '<br><br>';
//} else {
//	echo "Couldn't issue database query<br><br>";
	//print_r($response);
//	$wpdb->print_error();
//}

get_footer();
?>

