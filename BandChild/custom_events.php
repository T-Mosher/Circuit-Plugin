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
echo '<p>To enter an event, use your unit registration link</p>';
echo '<br>';

$query = "SELECT * FROM events WHERE event_active = 'T' ORDER BY event_date ASC";

$response = $wpdb->get_results($query, ARRAY_A);
 
if($response) {
	echo '<table style=bold>
		<tr>
		<td><b>Date</b></td>
		<td><b>Event Name</b></td>
		<td><b>Location</b></td>
		<td><b>Host</b></td>
		<td><b>Event Type</b></td>
		</tr>';
	
	foreach($response as $row) {
		// read the number of entries for each event
		//$event_entries = count($row['event_id']);
		$event_entries = 99;
		
		// populate each row of the event summary table
		echo '<tr>';
		echo '<td>' . $row['event_date'] . '</td>';
		echo '<td>' . $row['event_name'] . '</td>';
		echo '<td>' . $row['event_location'] . '</td>';
		echo '<td>' . $row['event_host'] . '</td>';
		//echo '<td>' . $row['event_capacity'] . '</td>';
		//echo '<td>' . $event_entries . '</td>';
		echo '<td>' . $row['event_type'] . '</td>';
 		echo '</tr>';
	}
	echo '</table>';
} else {
	echo "Couldn't issue database query<br><br>";
	//print_r($response);
	$wpdb->print_error();
}

get_footer();
?>

