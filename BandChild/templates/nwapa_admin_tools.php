<?php
/*
Template Name: NWAPA Admin Tools
May 2017:
Try adding some simple admin tools
*/

global $wpdb;

// display the page header
get_header();

echo '<h3>NWAPA administrator tools</h3>';

// how many event entries are there?
$query = "SELECT * from entries";
$entry_rslt = $wpdb->get_results($query, ARRAY_A);

// DEBUG
//echo '<br>';
//var_dump($entry_rslt);
//echo '<br>';

if (!empty($entry_rslt)) {
    //print_r($rslt);
    echo '<p>Number of event entries: '.count($entry_rslt).'</p>';
}

// how many log file records are there?
$query = "SELECT * from nwapa_log";
$rslt = $wpdb->get_results($query, ARRAY_A);

if (!empty($rslt)) {
    //print_r($rslt);
    echo '<p>Number of log entries: '.count($rslt).'</p>';
}

// how many entries are there for each active event?
$query = "SELECT event_id, event_name, event_capacity FROM events WHERE event_active = 'T' ORDER By event_date ASC";
$rslt = $wpdb->get_results($query, ARRAY_A);
if (!empty($rslt)) {
    echo '<h4>Entries for each event</h4>';
    echo "<table border='1'>";
    echo '<tr><th>Event Name</th><th>Entries</th><th>Capacity</th></tr>';
    foreach($rslt as $r) {
        $cnt = 0;
        foreach($entry_rslt as $entry) {
            //echo '<br>each comparison<br>';
            //var_dump($r);
            //echo '<br>';
            //var_dump($entry);
            if ($r['event_id'] ===  $entry['what_event']) {;
                $cnt = $cnt + 1;
            }
        }
        echo '<tr><td>'.$r['event_name'].'</td><td>'.$cnt.'</td><td>'.$r['event_capacity'].'</td></tr>';
    }
    echo '</table>';
}

//---------------   end of page -----------------
echo '<br><br>';
// display the page footer
get_footer();

?>